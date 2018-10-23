<?php
/**
 * 工具类，一些常用的函数放在这里
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/27
 * Time: 18:20
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;

use Yaf\Registry;

class Tool
{

    /**
     * 读取配置文件
     * @param string $item (foo.mysql.config)
     * @return array
     */
    public static function getConfig($item)
    {
        $config = Registry::get($item);
        if (!$config || empty($config)) {
            return [];
        }
        return $config;
    }

    /**
     * 设置动态配置（只在单次请求有效，请求结束失效）
     * @param $item
     * @param $value
     * @return bool
     */
    public static function setConfig($item, $value)
    {
        return Registry::set($item, $value);
    }

    /**
     * 获取当前域名
     * @return string
     */
    public static function getHttpHost()
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER = array_merge($_SERVER, ['HTTP_HOST' => '']);
        }
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];

        return $host;
    }

    /**
     * 获取服务器所在地
     * @return string
     */
    public static function getServerLocation()
    {
        if (isset($_SERVER['SERVER_LOCATION'])) {
            return $_SERVER['SERVER_LOCATION'];
        } else {
            return 'zh-cn';
        }
    }

    public static function getServerLocationISO3166()
    {
        if (isset($_SERVER['SERVER_LOCATION'])) {
            return $_SERVER['SERVER_LOCATION'];
        } else {
            return 'CHN';
        }
    }

    /**
     * 获取当前服务器名
     *
     * @return mixed
     */
    public static function getServerName()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER'])
            return $_SERVER['HTTP_X_FORWARDED_SERVER'];

        return $_SERVER['HTTP_HOST'];
    }

    /**
     * 获取用户IP地址
     *
     * @return mixed
     */
    public static function getUserIp()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR']) || preg_match('/^127\..*/i',
                                                                                                                                          trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i',
                                                                                                                                                                                       trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^192\.168\.*/i',
                                                                                                                                                                                                                                    trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i',
                                                                                                                                                                                                                                                                                 trim($_SERVER['REMOTE_ADDR'])))
        ) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                return $ips[0];
            } else
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    /**
     * 获取用户来源地址
     *
     * @return null
     */
    public static function getReferer()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        } else {
            return null;
        }
    }

    /**
     * 获取当前URL
     *
     * @return string
     */
    public static function getUrl()
    {
        $pageURL = 'http';

        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        }

        return $pageURL;
    }

    /**
     * 判断是否是手机访问
     * @return bool
     */
    public static function isMobile()
    {
        $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $useragentCommentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
        $checkSubstrs = function ($substrs, $text) {
            foreach ($substrs as $substr)
                if (false !== strpos($text, $substr)) {
                    return true;
                }

            return false;
        };

        $mobileOsList = [
            'Google Wireless Transcoder',
            'Windows CE',
            'WindowsCE',
            'Symbian',
            'Android',
            'armv6l',
            'armv5',
            'Mobile',
            'CentOS',
            'mowser',
            'AvantGo',
            'Opera Mobi',
            'J2ME/MIDP',
            'Smartphone',
            'Go.Web',
            'Palm',
            'iPAQ'
        ];
        $mobileTokenList = [
            'Profile/MIDP',
            'Configuration/CLDC-',
            '160×160',
            '176×220',
            '240×240',
            '240×320',
            '320×240',
            'UP.Browser',
            'UP.Link',
            'SymbianOS',
            'PalmOS',
            'PocketPC',
            'SonyEricsson',
            'Nokia',
            'BlackBerry',
            'Vodafone',
            'BenQ',
            'Novarra-Vision',
            'Iris',
            'NetFront',
            'HTC_',
            'Xda_',
            'SAMSUNG-SGH',
            'Wapaka',
            'DoCoMo',
            'iPhone',
            'iPod'
        ];
        if ($checkSubstrs($mobileOsList, $useragentCommentsblock) || $checkSubstrs($mobileTokenList, $useragent)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断是否是App访问
     * @return bool
     */
    public static function isApp()
    {
        $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $checkSubstrs = function ($substrs, $text) {
            foreach ($substrs as $substr)
                if (false !== strpos($text, $substr)) {
                    return true;
                }

            return false;
        };

        $availableApps = Registry::get('availableApps');
        if ($checkSubstrs($availableApps, $useragent)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取utf8字符串长度
     *
     * @param null $string
     *
     * @return int
     */
    public static function utf8StrLen($string = null)
    {
        preg_match_all("/./us", $string, $match);
        return count($match[0]);
    }


    /**
     * Xss过滤
     * @param $val
     * @return mixed
     */
    public static function RemoveXSS($val)
    {
        $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';

        for ($i = 0; $i < strlen($search); $i++) {
            $val = preg_replace('/(&#[x|X]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
            $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
        }
        $ra = [];
        $ra1 = [
            'javascript',
            'vbscript',
            'expression',
            'applet',
            'meta',
            'xml',
            'blink',
            'link',
            'style',
            'script',
            'embed',
            'object',
            'iframe',
            'frame',
            'frameset',
            'ilayer',
            'layer',
            'bgsound',
            'title',
            'base'
        ];
        $ra2 = [
            'onabort',
            'onactivate',
            'onafterprint',
            'onafterupdate',
            'onbeforeactivate',
            'onbeforecopy',
            'onbeforecut',
            'onbeforedeactivate',
            'onbeforeeditfocus',
            'onbeforepaste',
            'onbeforeprint',
            'onbeforeunload',
            'onbeforeupdate',
            'onblur',
            'onbounce',
            'oncellchange',
            'onchange',
            'onclick',
            'oncontextmenu',
            'oncontrolselect',
            'oncopy',
            'oncut',
            'ondataavailable',
            'ondatasetchanged',
            'ondatasetcomplete',
            'ondblclick',
            'ondeactivate',
            'ondrag',
            'ondragend',
            'ondragenter',
            'ondragleave',
            'ondragover',
            'ondragstart',
            'ondrop',
            'onerror',
            'onerrorupdate',
            'onfilterchange',
            'onfinish',
            'onfocus',
            'onfocusin',
            'onfocusout',
            'onhelp',
            'onkeydown',
            'onkeypress',
            'onkeyup',
            'onlayoutcomplete',
            'onload',
            'onlosecapture',
            'onmousedown',
            'onmouseenter',
            'onmouseleave',
            'onmousemove',
            'onmouseout',
            'onmouseover',
            'onmouseup',
            'onmousewheel',
            'onmove',
            'onmoveend',
            'onmovestart',
            'onpaste',
            'onpropertychange',
            'onreadystatechange',
            'onreset',
            'onresize',
            'onresizeend',
            'onresizestart',
            'onrowenter',
            'onrowexit',
            'onrowsdelete',
            'onrowsinserted',
            'onscroll',
            'onselect',
            'onselectionchange',
            'onselectstart',
            'onstart',
            'onstop',
            'onsubmit',
            'onunload'
        ];
        $ra11 = [];
        foreach ($ra1 as $item) {
            $ra11[] = $item . '>';
        }
        foreach ($ra1 as $item) {
            $ra12[] = '<' . $item;
        }
        $ra = array_merge($ra11, $ra12, $ra2);

        $found = true;
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $ra[$i] = $ra[$i] . '>';
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                        $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                        $pattern .= ')?';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val);
                if ($val_before == $val) {
                    $found = false;
                }
            }
        }
        return $val;
    }

    /**
     * 获取sso_token
     * @return string
     */
    public static function getSsoToken()
    {
        $sso_tk = Input::getQuery('sso_tk');
        if (empty($sso_tk)) {
            $sso_tk = Input::getQuery('HTTP_SSOTK');
        }
        return $sso_tk;
    }

    /**
     * 快速缓存，需要配置memcache,并且设置default默认缓存服务器
     * @param string $key
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public static function Cache($key, $value = '', $expire = 0)
    {
        $cache = Cache::init('default');
        if ($cache) {
            if (is_null($value)) {
                return $cache->rm($key);
            } elseif (!empty($value)) {
                return $cache->set($key, $value, $expire);
            } elseif ($value == '') {
                return $cache->get($key);
            }
        }
        return false;
    }

    /**
     * 获取位置
     */
    public static function getLocation()
    {
        if (isset($_SERVER['SERVER_LOCATION']) && $_SERVER['SERVER_LOCATION'] == 'USA') {
            return "USA";
        } elseif (isset($_SERVER['SERVER_LOCATION']) && $_SERVER['SERVER_LOCATION'] == 'hongkong') {
            return "HK";
        } else {
            return "CN";//默认都作为中国
        }
    }

    /**
     * 根据位置获取该位置对应语言
     */
    public static function selectPicBYLocation()
    {
        $mapping = [
            'USA'     => 'en',
            'HK'      => 'zh_hk',
            'default' => ''
        ];
        $location = self::getLocation();
        return isset($mapping[$location]) ? $mapping[$location] : $mapping['default'];
    }

    /**
     * 对邮箱地址打码
     * @param $email
     * @return string
     */
    public static function formatEmail($email)
    {
        if (strpos($email, '@') >= 4) {
            return substr($email, 0, 2) . str_repeat('*', strpos($email, '@') - 4) . substr($email,
                    (strpos($email, '@') - 2));
        } else {
            return $email;
        }
    }

    /**
     * 对手机号码、电话号码打码
     * @param $mobile
     * @return mixed
     */
    public static function formatMobile($mobile)
    {
        $len = strlen($mobile);
        $res = $mobile;
        switch ($len) {
            case 11:
                $res = preg_replace("/(\d{3})(\d{4})(\d{4})/", "$1****$3", $mobile);
                break;
            case 12:
                //006586995835
                $res = preg_replace("/(\d{7})(\d{3})(\d{2})/", "$1***$3", $mobile);
                break;
            case 13:
                //0064212618328
                $res = preg_replace("/(\d{7})(\d{3})(\d{3})/", "$1***$3", $mobile);
                break;
            case 14:
                //00886918901515
                $res = preg_replace("/(\d{8})(\d{3})(\d{3})/", "$1***$3", $mobile);
                break;
            default:
                $res = $mobile;
        }
        return $res;
    }

    /**
     * 根据邮箱地址，返回邮箱登录地址
     * @param $email
     * @return string
     */
    public static function getMailWebSite($email)
    {
        $tmp_url = trim(strrchr($email, '@'), '@');
        $emailArr = [
            'yahoo.com.cn' => 'http://mail.cn.yahoo.com ',
            'yahoo.cn'     => 'http://mail.cn.yahoo.com ',
            'sina.com'     => 'http://mail.sina.com.cn',
            'sina.cn'      => 'http://mail.sina.com.cn',
            'vip.sina.com' => 'http://vip.sina.com',
            'gmail.com'    => 'http://mail.google.com',
            '189.cn'       => 'http://mail.189.cn/webmail/',
            '139.com'      => 'http://mail.10086.cn/',
            'hotmail.com'  => 'http://mail.live.com/',
            'vip.163.com'  => 'http://mail.163.com/',
        ];
        if (isset($emailArr[$tmp_url])) {
            $emailUrl = $emailArr[$tmp_url];
        } else {
            $emailUrl = 'http://mail.' . $tmp_url;
        }
        return $emailUrl;
    }

    /**
     * 调向登录页面
     */
    public static function getJumpLoginPage()
    {
        $loginUrl = 'https://sso.le.com';
        $lang = strtolower($GLOBALS['language']);
        if (!self::isMobile()) {
            if ($lang == "zh-cn") {
                $redirectUrl = $loginUrl . '/?next_action=';
            } else {
                $redirectUrl = $loginUrl . '/userGlobal/login?next_action=';
            }
        } else {
            if ($lang == "zh-cn") {
                $redirectUrl = $loginUrl . '/user/mLoginHome?next_action=';
            } else {
                $redirectUrl = $loginUrl . '/userGlobal/mlogin?next_action=';
            }
        }
        $currUrl = self::getUrl();
        $redirectUrl .= urlencode($currUrl);
        return $redirectUrl;
    }

    /**
     * 计算分页信息
     * @param int $currPage
     * @param int $pageSize
     * @param array $recordTotal
     * @return array|bool
     */
    public static function getPageInfo($currPage = 1, $pageSize = 16, $recordTotal = [])
    {
        if (empty($recordTotal)) {
            return false;
        }
        $pageInfo = [];

        $pageInfo['pagesize'] = $pageSize;
        $pageInfo['totalrecord'] = $recordTotal;
        $pageTotal = ceil($recordTotal / $pageSize);
        $pageInfo['totalpage'] = $pageTotal;
        if ($currPage >= $pageTotal) {
            $pageInfo['currpage'] = $pageTotal;
        } else {
            $pageInfo['currpage'] = $currPage;
        }
        if ($currPage == 1) {
            $pageInfo['isfirst'] = true;
        } else {
            $pageInfo['isfirst'] = false;
        }
        if ($currPage >= $pageTotal) {
            $pageInfo['islast'] = true;
        } else {
            $pageInfo['islast'] = false;
        }

        if ($currPage > 1) {
            $pageInfo['previous'] = $currPage - 1;
        } else {
            $pageInfo['previous'] = '';
        }

        if ($currPage < $pageTotal) {
            $pageInfo['next'] = $currPage + 1;
        } else {
            $pageInfo['next'] = '';
        }

        return $pageInfo;
    }

    /**
     * 获取当前毫秒数
     */
    public static function getMilliSecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 计算哈希值
     */
    public static function string2hash($value, $base)
    {
        if (is_numeric($value)) {
            return $value % $base;
        }
        $crc = sprintf("%u", crc32($value));
        return $crc % $base;

    }

    /**
     * 手机打码
     * 137*****123
     */
    public static function mobileMask($mobile)
    {
        $len = strlen($mobile);
        if (preg_match("/^00\d{0,}$/", $mobile)) {
            if ($len > 6) { //海外手机
                $mobile = substr_replace($mobile, str_repeat('*', $len - 8), 4, $len - 8);
            }
        } else { //大陆手机
            if ($len > 4) {
                $mobile = substr_replace($mobile, str_repeat('*', $len - 6), 3, $len - 6);
            }
        }
        return $mobile;
    }


    /**
     * 获取当前服务器已用内存
     * @param $size
     * @return string
     */
    public static function getMemoryUsage($size)
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }
}