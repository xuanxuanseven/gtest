<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/15
 * Time: 上午10:09
 */
namespace Lephp\Cli;
// define STDIN, STDOUT and STDERR if the PHP SAPI did not define them (e.g. creating console application in web env)
// http://php.net/manual/en/features.commandline.io-streams.php
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));
defined('STDERR') or define('STDERR', fopen('php://stderr', 'w'));


class Application extends \Lephp\Core\Application
{

}