<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/16
 * Time: 下午4:04
 */

namespace Lephp\Helpers\Kafka;


use Lephp\Core\Component;
use Rdkafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\TopicConf;

class Consumer extends Component
{
    /**
     * @var string $broker_list 逗号分隔
     */
    public $broker_list = '';
    /**
     * @var string $group_id
     */
    public $group_id = 'lephp-' . APP_NAME;
    /**
     * @var array $topics
     */
    public $topics;

    /**
     * @var \RdKafka\Conf $rdKafkaConf
     */
    public $rdKafkaConf;

    /**
     * @var \RdKafka\TopicConf $topicConf
     */
    public $topicConf;
    /**
     * @var \RdKafka\KafkaConsumer $consumer
     */
    public $consumer;

    /**
     * 消费超时时间
     * @var int $consume_timeout
     */
    public $consume_timeout = 120 * 1000;

    /**
     * topic 配置
     * @ref https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
     * @var array
     */
    public $topicConfigurations = [];

    /**
     * init
     */
    public function init()
    {
        parent::init();
        $this->initRdKafkaConf()->initRdKafkaTopicConf()->initRdKafkaConsumer();
    }

    /**
     * @return $this
     */
    public function initRdKafkaConf()
    {
        $this->rdKafkaConf = new Conf();
        $this->rdKafkaConf->setRebalanceCb('\Lephp\Helpers\Kafka\Consumer::rebalanceCallback');
        $this->rdKafkaConf->set('group.id', $this->group_id);
        $this->rdKafkaConf->set('metadata.broker.list', $this->broker_list);
        return $this;
    }

    /**
     * 默认从最新Offset开始
     * @return $this
     */
    public function initRdKafkaTopicConf()
    {
        $this->topicConf = new TopicConf();
        $this->topicConf->set('auto.offset.reset', 'latest');
        foreach ($this->topicConfigurations as $key => $topicConfiguration) {
            $this->topicConf->set($key, $topicConfiguration);
        }
        $this->rdKafkaConf->setDefaultTopicConf($this->topicConf);
        return $this;
    }

    /**
     * @return $this
     */
    public function initRdKafkaConsumer()
    {
        $this->consumer = new KafkaConsumer($this->rdKafkaConf);
        $this->consumer->subscribe($this->topics);
        return $this;
    }

    /**
     * @return \RdKafka\Message
     */
    public function consume()
    {
        return $this->consumer->consume($this->consume_timeout);
    }

    /**
     * @param KafkaConsumer $kafka
     * @param $err
     * @param array $partitions
     * @throws \Exception
     *
     */
    public static function rebalanceCallback(KafkaConsumer $kafka, $err, $partitions = [])
    {
        switch ($err) {
            case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                echo "Assign: ";
                var_dump($partitions);
                $kafka->assign($partitions);
                break;

            case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                echo "Revoke: ";
                var_dump($partitions);
                $kafka->assign(NULL);
                break;
            default:
                throw new \Exception($err);
        }
    }

    /**
     * @param string $broker_list
     * @return Consumer
     */
    public function setBrokerList(string $broker_list): Consumer
    {
        $this->broker_list = $broker_list;
        return $this;
    }

    /**
     * @return string
     */
    public function getBrokerList(): string
    {
        return $this->broker_list;
    }

    /**
     * @param string $group_id
     * @return Consumer
     */
    public function setGroupId(string $group_id): Consumer
    {
        $this->group_id = $group_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroupId(): string
    {
        return $this->group_id;
    }

    /**
     * @param mixed $topics
     * @return Consumer
     */
    public function setTopics($topics)
    {
        $this->topics = $topics;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * @param int $consume_timeout
     * @return Consumer
     */
    public function setConsumeTimeout(int $consume_timeout): Consumer
    {
        $this->consume_timeout = $consume_timeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getConsumeTimeout(): int
    {
        return $this->consume_timeout;
    }

    /**
     * @param array $topicConfigurations
     * @return Consumer
     */
    public function setTopicConfigurations(array $topicConfigurations): Consumer
    {
        $this->topicConfigurations = $topicConfigurations;
        return $this;
    }

    /**
     * @return array
     */
    public function getTopicConfigurations(): array
    {
        return $this->topicConfigurations;
    }
}