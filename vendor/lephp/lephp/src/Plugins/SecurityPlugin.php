<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2017/2/9
 * Time: 下午5:51
 */

namespace Lephp\Plugins;


use Lephp\Core\Security;
use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

class SecurityPlugin extends Plugin_Abstract
{
    public $ignoreList = [];

    public function __construct($ignoreList = [])
    {
        $this->ignoreList = $ignoreList;
    }

    public function routerStartup(Request_Abstract $request, Response_Abstract $response)
    {
        $filter = [
            '_GET',
            '_POST',
            '_REQUEST',
            '_SESSION',
            '_COOKIE'
        ];
        $Security = new Security();
        foreach ($filter as $item) {
            if (!empty($GLOBALS[$item])) {
                foreach ($GLOBALS[$item] as $key => $value) {
                    if (!empty($value) && !in_array($key, $this->ignoreList)) {
                        $GLOBALS[$item][$key] = $Security->filter_it($value);
                    }
                }
            }
        }
    }
}