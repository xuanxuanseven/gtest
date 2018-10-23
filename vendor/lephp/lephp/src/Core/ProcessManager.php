<?php
/**
 * Created by PhpStorm.
 * User: wanglishuang
 * Date: 2016/6/17
 * Time: 16:11
 * 此类用于进程管理
 * 负责进程的定时关闭和处理请求达到一定次数关闭进程
 */
namespace Lephp\Core;
class ProcessManager {

    /**
     * 工作量计数器
     * @var int
     */
    protected $cnt_job = 0;
    /**
     * 开始时间
     * @var int
     */
    protected $time_start = 0;

    /**
     * 最大生存时间（单位： 秒），0为无限制
     * @var int
     */
    protected $time_to_live = 0;
    /**
     * 最大工作量（单位：个），0为无限制
     * @var int
     */
    protected $max_job_cnt = 0;

    protected $event = array();

    protected $should_stop = false;

    protected $signal_enable = false; //是否接收信号

    public $debug = false;

    /**
     * @param int $max_jobs_cnt
     * @param int $time_to_live
     */
    public function __construct($max_jobs_cnt = 0, $time_to_live = 0){
        $this->max_job_cnt = $max_jobs_cnt;
        $this->time_to_live = $time_to_live;
        $this->time_start = time();
    }

    public function set_job_cnt($cnt) {
        $this->cnt_job = $cnt;
    }

    public function get_job_cnt() {
        return $this->cnt_job;
    }

    public function set_time_to_live($time) {
        $this->time_to_live = $time;
    }

    public function get_time_to_live() {
        return $this->time_to_live;
    }

    public function get_time_start() {
        return $this->time_start;
    }

    public function inc_job_cnt($cnt = 1) {
        $this->cnt_job += $cnt;
    }

    public function reg_event($key, $callback) {
        if (!is_callable($callback)) {
            throw new Exception("callback can not callable");
        }
        $this->event[$key] = $callback;
    }

    public function set_signal_enable() {
        if ($this->signal_enable) return true;
        $this->signal_enable = true;
        pcntl_signal(SIGTERM, array($this, "signal_handler")) || die("set signal SIGTERM fail"); // 信号处理会消耗大量的cpu
        return true;
    }

    /**
     * 检查是否需要重启，需要则重启,需要停掉则停掉
     * @param array $arr 当前状态
     */
    public function check_for_stop(array $arr = array()) {
        if ($this->signal_enable) pcntl_signal_dispatch();
        if ($this->should_stop) {
            $this->debug( "stop by signal:", posix_getpid());
            $this->stop($arr);
        }
        if ($this->max_job_cnt > 0 && $this->cnt_job >= $this->max_job_cnt) {
            $this->debug( "stop by cnt:", posix_getpid());
            $this->stop($arr);
        }
        if ($this->time_to_live > 0 && (time() - $this->time_start >= $this->time_to_live)) {
            $this->debug( "stop by time:", posix_getpid());
            $this->stop($arr);
        }
    }

    public function stop(Array $arr = array()) {
        if (isset($this->event['stop']) && is_callable($this->event['stop'])) {
            call_user_func_array($this->event['stop'], array($arr));
        }
        exit();
    }


    public function signal_handler($signo) {
        switch($signo) {
            case SIGTERM:
                $this->should_stop = true;
        }
    }

    public function debug($msg) {
        if ($this->debug) {
            echo $msg ,"\n";
        }
    }
}