<?php
/**
* 测试相关
*/
//namespace app\module1\controller;
//use think\View;
use Lephp\Core\Controllers;
class GetindexController extends Controllers
{
    // 选择 查看 普通版 | RSA版
    public function indexAction(){
        echo '
            <h3>请选择演示的版本</h3>
            <ul>
                <a href="/tv/Getindex/verify" target="_blank"><li>普通版</li></a>
                <a href="/tv/Getindex/verify_rsa" target="_blank"><li>RSA版 [模拟登陆]</li></a>
            </ul>
        ';
    }
    

    // 普通版 => 只实现 验证验证码
    public function verifyAction(){
        $this->_view->display('index/verify.tpl');
       
    }

    // RSA版 => 验证 验证码与帐号信息 
    public function verify_rsaAction(){
        //$v = new View();
        $this->_view->display('index/verify_rsa.tpl');
        //echo $v->fetch('Index/verify_rsa');
    }

    /**
    * 请将 公钥与私钥 放在 /Crypt/rsa 目录下
    * 获取到的对应 hlz_rsa.js 会存于 /Crypt/rsa 目录中
    * 有关 hlz_rsa.js 的使用方法  详见  https://github.com/HaleyLeoZhang/rsa-js-php
    */
    public function get_rsa_jsAction(){
        \Crypt\Rsa::bulid_rsa_js();
    }


}
