<?php
/**
 * Created by PhpStorm.
 * User: wanglishuang
 * Date: 2016/6/7
 * Time: 19:21
 */

namespace Lephp\Core;

class Daemon
{
    private $cnt = 1;
    private $current_jobs = array();
    private $_job;

    public function __construct($job, $cnt = 1) {
        if (!is_callable($job)){
            throw new Exception("job is can not callable");
        }
        $this->_job = $job;
        $this->cnt = $cnt;
        $this->_daemonize();
        $this->_main();
    }

    private function _daemonize() {
        $pid = pcntl_fork();
        if ($pid < 0) {
            die("fork fail\n");
        }else if($pid) {
            exit;
        }
        posix_setsid();
    }

    private function _main() {
        $num = $this->cnt;
        while($num-- > 0) {
            $this->fork();
        }
        while(true) {
            // ppid can receive signal when wait hang for child, so must set nohang
            $pid = pcntl_wait($status, WNOHANG);  // or WNOHANG and use sleep

            if($pid && isset($this->current_jobs[$pid])){
                $exitCode = pcntl_wexitstatus($status);
                $this->debug("$pid exited with status ".$exitCode);

                unset($this->current_jobs[$pid]);
                $this->fork();
                $this->debug($this->current_jobs);
            } else {
                sleep(1);
            }
        }
    }
    public function fork() {
        $pid = pcntl_fork();
        if ($pid < 0) {
            throw new Exception("fork fail");
        } elseif($pid == 0) { //child
            $this->job(); exit;
        } else { // parent
            $this->current_jobs[$pid] = array('start_time' => time());
        }
    }

    public function job(){
        call_user_func($this->_job);
    }

    public function debug($msg) {
        $file = "/tmp/daemon_".posix_getpid();
        if (file_exists($file) && filesize($file) > 10 * 1024 * 1024) {
            unlink($file);
        }
        if (!is_string($msg)) $msg = json_encode($msg);
        $msg = date("Y-m-d H:i:s")."\t". $msg."\n";
        file_put_contents($file, $msg, FILE_APPEND);
    }
}