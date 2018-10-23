<?php
/**
 * 配置文件（除了常量以外的配置，必须使用Registry::set注册）
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/27
 * Time: 19:30
 * @copyright LeEco
 * @since 1.0.0
 */

use L10N\L10N;
use Yaf\Application;
use Yaf\Registry;



Registry::set(
    'domain',
    [
        'lxy.phpgtest.com' => 'mtest',
    ]
);

Registry::set(
    'langVar',
    [
        'cookie' => 'language',
        'detect' => 'language'
    ]
);

Registry::set('DEFAULT_ERROR_PAGE', 'www.le.com/error');


/**
 * 路由规则
 */
$version = 'v1';
//if (strpos($_SERVER['HTTP_HOST'], 'i.cp21.ott.cibntv.net') !== false) {
if (strpos($_SERVER['HTTP_HOST'], 'lxy.phpgtest.com') !== false) {
    $module = 'mtest';
} else {
    $module = 'mtest';
}

$config = [
    'v1' => [
        'module'     => $module,
        'urlManager' => [
            'rules' => [
                'mtest'                     => [
                    'match'   => '#/tv/?([^/]*)?/?([^/]*)?#i',
                    'route'   => [
                        'module'     => 'mtest',
                        'controller' => ':controller',
                        'action'     => ':action'
                    ],
                    'map'     => [
                        1 => 'controller',
                        2 => 'action'
                    ],
                    'verify'  => [],
                    'plugins' => [
                        'smarty' => [
                            'class'                   => '\Lephp\Plugins\Smarty\SmartyAdapter',
                            'callableObject'          => Application::app()->getDispatcher(),
                            'callablePreInjectMethod' => 'disableView',
                            'callableMethod'          => 'setView',
                        ],
                        'l10n'   => [
                            'class' => '\L10N\L10NAdapter',
                        ],
                    ],
                ]
            ]
        ]
    ]
];

if (isset($config[$version])) {
    Registry::set('moduleConfig', $config['v1']);
}
