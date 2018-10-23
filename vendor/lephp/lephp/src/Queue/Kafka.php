<?php
/**
 * Created by PhpStorm.
 * User: wanglishuang
 * Date: 2016/6/15
 * Time: 11:13
 */

namespace Lephp\Queue;

use Lephp\Core\Exception;

class Kafka
{
    /**
     * @var RdKafka\KafkaConsumer
     */
    protected $high_consumer = null;
    /**
     * @var RdKafka\Consumer
     */
    protected $low_consumer = null;
    /**
     * @var RdKafka\Producer
     */
    protected $producer = null;
    /**
     * @var array
     */
    protected $topics = array();

    protected $produce_topic_list = array();
    /**
     * @var array
     */
    protected $partitions = array();

    protected $high_consume = null;
    protected $high_conf = null;
    protected $high_topic_conf = null;

    protected $low_topic = null;

    private $err_pid = '';

    protected static $settings = array(
        "socket.timeout.ms"				=>	"60000",
        "socket.keepalive.enable"		=>	"false",
        "socket.max.fails"				=>	"3",
        "queued.min.messages"			=>	"100000",
        "queued.max.messages.kbytes"	=>	"1000000",
        "fetch.wait.max.ms"				=>	"1000",
        "fetch.message.max.bytes"		=>	"1048576",
    );

    protected static $topicSettings = array(
        //"group.id"						=>	"user-growth-rdkafka",
        "request.timeout.ms"			=>	"6000",
        "message.timeout.ms"			=>	"300000",
        "auto.commit.enable"			=>	"true",
        "auto.commit.interval.ms"		=>	"10000",
        "auto.offset.reset"				=>	"smallest", //"largest",
        "offset.store.method"			=>	"broker",
        "offset.store.path"				=>	"/tmp",
        "offset.store.sync.interval.ms"	=>	"10000",
    );

    function debug($str) {
        echo "<pre>\n";
        print_r($str);
        echo "\n<pre>";
    }
    public function set_topics($topics) {
        $this->topics = $topics;
    }
    public function get_topics() {
        return $this->topics;
    }
    /**
     * 设置参数，这里可以覆盖上面的默认设置(需要在调用其他方法之前调用)
     * @param $config
     * @return bool
     */
    public function set_global_config($config) {
        self::$settings = array_merge(self::$settings, $config);
        return true;
    }

    /**
     * 设置参数，这里可以覆盖上面的默认设置(需要在调用其他方法之前调用)
     * @param $config
     * @return bool
     */
    public function set_topic_config($config) {
        self::$topicSettings = array_merge(self::$topicSettings, $config);
        return true;
    }
    /**
     * global setting
     * @param $key
     * @param $value
     */
    public function set_high_option($key, $value) {
        $this->get_high_conf()->set($key, $value);
    }

    private function get_high_conf() {
        if ($this->high_conf === null) {
            if (empty(self::$settings['group.id']) || empty(self::$settings['metadata.broker.list'])) {
                throw new Exception("kafka need group id and metadata.broker.list");
            }

            $this->high_conf = new \RdKafka\Conf();
            $this->high_conf->setRebalanceCb(function ($rk, $err, $partitions) {
//                echo posix_getpid()."\n";
//                $this->debug($partitions);
                switch ($err) {
                    case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                        $rk->assign($partitions);
                        break;
                    case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                        $rk->assign(null);
                        break;
                    case RD_KAFKA_RESP_ERR_REBALANCE_IN_PROGRESS:
                        throw new Exception('kafka reblance error');
                        break;
                    default:
                        throw  new Exception('kafka reblance unknow error');
                }
            });

            foreach (self::$settings as $k => $v) {
                $this->set_high_option($k, $v);
            }
        }
        return  $this->high_conf;
    }

    /**
     * topic setting
     * @param $key
     * @param $value
     */
    public function set_high_topic_option($key, $value) {
        $this->get_high_topic_conf()->set($key, $value);
    }
    private function get_high_topic_conf() {
        if ($this->high_topic_conf === null) {
            $this->high_topic_conf = new \RdKafka\TopicConf();
            foreach (self::$topicSettings as $k => $v) {
                $this->set_high_topic_option($k, $v);
            }
        }
        return $this->high_topic_conf;
    }
    private function get_high_queue() {
        if ($this->high_consume === null) {
            $high_config = $this->get_high_conf();
            $high_topic_conf = $this->get_high_topic_conf();
            $high_config->setDefaultTopicConf($high_topic_conf);

            $this->high_consume = new \RdKafka\KafkaConsumer($high_config);

            $this->high_consume->subscribe($this->topics);
        }

        return $this->high_consume;
    }

    private function unset_high_queue() {
        $this->high_consume = null;
        $this->high_topic_conf = null;
        $this->high_conf = null;
    }

    /**
     * 高级消费数据
     * @param int $time_out
     * @return string
     * @throws Exception
     */
    public function high_consume($time_out=3000) {
        $queue = $this->get_high_queue();
        try{
            $msg = $queue->consume($time_out);
            if ($msg->err == '-185') {
                echo posix_getpid()."\n";
                exit();
            }
//            $this->debug($msg);
        }catch (Exception $e) {
            echo $e->getMessage()."|".$e->getCode()."\n";
        }

        $msg = new \stdClass();
        $msg->err = 0;
        $msg->topic_name = 'user-growth-test';
        $msg->partition = 0;
        $msg->payload = 17011;

        return $msg;
    }

    /**
     * 低级消费数据
     */
    public function low_consume() {
        if ($this->low_topic == null) {
            $conf = new \RdKafka\Conf();
            $conf->set('group.id', 'user-growth-rdkafka');
            $rk = new \RdKafka\Consumer($conf);
            $rk->addBrokers("10.185.31.187:9092,10.185.31.193:9092,10.185.31.195:9092");
            $topicConf = new \RdKafka\TopicConf();
            $topicConf->set('auto.commit.interval.ms', 100);
            $topicConf->set('offset.store.method', 'broker');
            $topicConf->set('offset.store.path', '/tmp');
            $topicConf->set('auto.offset.reset', 'smallest');
            $topic = $rk->newTopic("user-growth-test", $topicConf);
            $topic->consumeStart(1, RD_KAFKA_OFFSET_STORED);
            $this->low_topic = $topic;
        }

        $message = $this->low_topic->consume(1, 120*10000);
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                var_dump($message);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                echo "No more messages; will wait for more\n";
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                echo "Timed out\n";
                break;
            default:
                throw new \Exception($message->errstr(), $message->err);
                break;
        }
        return $message;
    }

    /**
     * 生产者topic
     * @param $topic
     * @param $brokers
     * @return mixed
     */
    public function get_produce_topic($topic, $brokers) {
        if (empty($this->produce_topic_list[$topic])) {
            $rk = new \RdKafka\Producer();
            $rk->setLogLevel(LOG_DEBUG);
            $rk->addBrokers($brokers);
            $topic_config = $this->get_high_topic_conf();
            $this->produce_topic_list[$topic] = $rk->newTopic($topic, $topic_config);
        }
        return $this->produce_topic_list[$topic];
    }

    /**
     * 生产者
     * @string $topic   "test"
     * @param $brokers  "1,1.1.1,2.2.2.2"
     * @param $message
     * @return void(文档上没有写有返回值)
     */
    public function produce($topic, $brokers, $message) {
        if (empty($topic) || empty($brokers)) {
            throw new Exception("producer need topic and brokers");
        }
        $topic = $this->get_produce_topic($topic, $brokers);

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
    }

    /**
     * 同步提交offset
     */
    public function syn_commit_offset() {
        $this->get_high_queue()->commit();
    }
    /**
     * 异步提交offset
     */
    public function asyn_commit_offset() {
        $this->get_high_queue()->commitAsync();
    }

    public function get_brokers_list() {

    }

    public function get_topic_list() {

    }

    public function get_broker_id() {
        $meta_data = $this->get_high_queue()->getMetadata(true);
        return $meta_data->getOrigBrokerId();
    }

    public function get_broker_name() {
        $meta_data = $this->get_high_queue()->getMetadata(true);
        return $meta_data->getOrigBrokerName();
    }

}