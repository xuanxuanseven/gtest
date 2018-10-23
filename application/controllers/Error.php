<?php

/**
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/5/18
 * Time: 18:49
 * @copyright LeEco
 * @since 1.0.0
 */
class ErrorController extends Core\Controllers
{

    public function errorAction($exception)
    {
        var_dump($exception);die();
    }
}
