<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 7/13/16
 * Time: 10:25 AM
 * @copyright LeEco
 * @since 1.0.0
 */
namespace L10N;

use L10N\ISO3166\ISO3166;
use L10N\ISO639\ISO639;
use Lephp\Core\Component;
use Lephp\Core\Cookie;
use Lephp\Core\Tool;
use Yaf\Application;
use Yaf\Loader;
use Yaf\Registry;

class L10NAdapter extends Component
{
    public $area;
    /**
     * @var ISO639 $iso639
     */
    public $iso639;
    public $iso639Define;
    /**
     * @var ISO3166 $iso3166
     */
    public $iso3166;
    public $language;

    /**
     * @var array $locations
     */
    public $locations;

    /**
     * @var array $locales
     */
    public $locales;

    public function init()
    {
        self::detectLanguage();
        self::detectServiceArea();
        global $language, $iso639Define;
        $this->setIso639Define($iso639Define)
            ->setLanguage($language)
            ->setIso639(new ISO639())
            ->setIso3166(new ISO3166())
            ->loadLocalesConfig()
            ->loadLocationsConfig()
            ->loadMoFile()
            ->loadL10NServiceConfig();
    }

    /**
     * @return L10NAdapter
     */
    public function loadLocalesConfig()
    {
        $locales = [];
        $locales[] = $this->getIso639Define();
        /**
         * @var \Yaf\Config_Abstract $appConf
         */
        $appConf = Application::app()->getConfig();

        if (isset($appConf['l10n']['defaultLanguage'])
            &&
            0 !== strcasecmp($appConf['l10n']['defaultLanguage'], $this->getLanguage())
        ) {
            $locales[] = $this->getIso639()->get($appConf['l10n']['defaultLanguage']);
        }
        return $this->setLocales($locales);
    }

    /**
     * @return L10NAdapter
     */
    public function loadLocationsConfig()
    {
        $locations = [];
        /**
         * @var \Yaf\Config_Abstract $appConf
         */
        $appConf = Application::app()->getConfig();
        $moduleConfig = Registry::get('moduleConfig');
        $moduleName = ucfirst($moduleConfig['module']);
        if (isset($appConf['l10n']['commonL10NPath'])) {
            $locations[] = $appConf['l10n']['commonL10NPath'];
        }
        if (is_dir(APPLICATION_PATH . '/' . APP_NAME . '/modules/' . $moduleName . '/l10n')) {
            $locations[] = APPLICATION_PATH . '/' . APP_NAME . '/modules/' . $moduleName . '/l10n';
        }
        return $this->setLocations($locations);
    }

    /**
     * Load .mo file
     */
    public function loadMoFile()
    {
        foreach ($this->getLocales() as $locale) {
            foreach ($this->getLocations() as $location) {
                if (file_exists($location . "/languages/" . $locale['http_name'] . '.mo')) {
                    L10N::loadTextDomain('default', $location . "/languages/" . $locale['http_name'] . '.mo');
                }
            }
        }
        return $this;
    }

    /**
     * Load localization-service configurations
     * 加载服务本地化配置
     */
    public function loadL10NServiceConfig()
    {
        if (isset($_REQUEST['sale_area'])) {
            $saleArea = $_REQUEST['sale_area'];
            try {
                $saleArea_ISO3166 = $this->getIso3166()->get($saleArea);
                foreach ($this->locations as $location) {
                    if (file_exists("{$location}/conf/{$saleArea_ISO3166['alpha3']}.php")) {
                        Loader::import("{$location}/conf/{$saleArea_ISO3166['alpha3']}.php");
                    }
                }
            } catch (\Exception $e) {
                foreach ($this->getLocations() as $location) {
                    if (file_exists("{$location}/conf/default.php")) {
                        Loader::import("{$location}/conf/default.php");
                    }
                }
            }
        } else {
            //location define in nginx.conf
            $currentArea = self::detectServiceArea();
            try {
                $saleArea_ISO3166 = $this->getIso3166()->get($currentArea);
                foreach ($this->locations as $location) {
                    if (file_exists("{$location}/conf/{$saleArea_ISO3166['alpha3']}.php")) {
                        Loader::import("{$location}/conf/{$saleArea_ISO3166['alpha3']}.php");
                    }
                }
            } catch (\Exception $e) {
                foreach ($this->getLocations() as $location) {
                    if (file_exists("{$location}/conf/default.php")) {
                        Loader::import("{$location}/conf/default.php");
                    }
                }
            }
        }
    }

    /**
     * 自动侦测设置获取语言选择
     * 本方法会注册全局变量 $language 作为当前会话语言
     */
    public static function detectLanguage()
    {
        // 自动侦测设置获取语言选择
        global $language;
        global $iso639Define;
        $langVar = Tool::getConfig('langVar');
        $langCookieVar = $langVar['cookie'];
        $langDetectVar = $langVar['detect'];
        if (self::detectRequestLanguage($langDetectVar, $langCookieVar)) {
            $language = self::detectRequestLanguage($langDetectVar, $langCookieVar);
        } elseif (self::detectCookieSettingLanguage($langCookieVar)) {
            $language = self::detectCookieSettingLanguage($langCookieVar);
        } elseif (self::defaultLanguage($langCookieVar)) {
            $language = self::defaultLanguage($langCookieVar);
        } elseif (self::detectHttpAcceptLanguage($langCookieVar)) {
            $language = self::detectHttpAcceptLanguage($langCookieVar);
        } else {
            $language = 'zh-cn';
        }
        $language = str_replace('_', '-', $language);
        $iso639Define = self::isValidLanguage($language);
        if (!$iso639Define) {
            $language = self::defaultLanguage($langCookieVar);
        }
    }

    /**
     * @param $language
     * @return bool
     */
    public static function isValidLanguage($language)
    {
        $iso639 = new ISO639();
        try {
            return $iso639->isValid($language);
        } catch (\OutOfBoundsException $e) {
            return false;
        }
    }

    /**
     * @param $langCookieVar
     * @return bool | String
     */
    public static function defaultLanguage($langCookieVar)
    {
        $l10nConfiguration = Tool::getConfig('application');
        if (isset($l10nConfiguration['l10n']['defaultLanguage'])) {
            $language = strtolower($l10nConfiguration['l10n']['defaultLanguage']);
            Cookie::set($langCookieVar, $language, [
                'expire' => 3600,
                'domain' => '.le.com'
            ]);
            return $language;
        } else {
            $serverLocation = Tool::getServerLocationISO3166();
            $iso639 = new ISO639();
            try {
                $language = $iso639->get($serverLocation);
                if (isset($language['http_name'])) {
                    Cookie::set($langCookieVar, $language['http_name'], [
                        'expire' => 3600,
                        'domain' => '.le.com'
                    ]);
                    return $language['http_name'];
                } else
                    return false;

            } catch (\Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param $langCookieVar
     * @return bool
     */
    public static function detectHttpAcceptLanguage($langCookieVar)
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // 自动侦测浏览器语言
            preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
            $language = strtolower($matches[1]);
            Cookie::set($langCookieVar, $language, [
                'expire' => 3600,
                'domain' => '.le.com'
            ]);
            return $language;
        }
        return false;
    }

    /**
     * @param $langCookieVar
     * @return bool
     */
    public static function detectCookieSettingLanguage($langCookieVar)
    {
        if (Cookie::get($langCookieVar)) {
            $language = strtolower(Cookie::get($langCookieVar));
            return $language;
        }
        return false;
    }

    /**
     * @param $langDetectVar
     * @param $langCookieVar
     * @return bool
     */
    public static function detectRequestLanguage($langDetectVar, $langCookieVar)
    {
        if (isset($_REQUEST[$langDetectVar])) {
            $language = strtolower($_REQUEST[$langDetectVar]);
            $language = str_replace('_', '-', $language);
            $iso639 = new ISO639();
            try {
                $language = $iso639->get($language);
                if (isset($language['http_name'])) {
                    Cookie::set($langCookieVar, $language['http_name'], [
                        'expire' => 3600,
                        'domain' => '.le.com'
                    ]);
                    return $language['http_name'];
                } else
                    return false;

            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @param String $serviceAreaCallableFunction 确定服务区域的规则
     * @return mixed|string
     * 检测当前请求所在区域
     */
    public static function detectServiceArea($serviceAreaCallableFunction = null)
    {
        if (!is_null($serviceAreaCallableFunction) && call_user_func($serviceAreaCallableFunction)) {
            $serviceArea = call_user_func($serviceAreaCallableFunction);
            $iso3166 = new ISO3166();
            $serviceAreaISO3166 = $iso3166->get($serviceArea);
            Cookie::set('l10n_service_area', $serviceAreaISO3166['alpha3']);
            return $serviceAreaISO3166['alpha3'];
        } elseif (!is_null(Cookie::get('l10n_service_area'))) {
            return Cookie::get('l10n_service_area');
        } else {
            Cookie::set('l10n_service_area', Tool::getServerLocationISO3166());
            return Tool::getServerLocationISO3166();
        }
    }

    /**
     * @param mixed $area
     * @return L10NAdapter
     */
    public function setArea($area)
    {
        $this->area = $area;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param mixed $iso639
     * @return L10NAdapter
     */
    public function setIso639($iso639)
    {
        $this->iso639 = $iso639;
        return $this;
    }

    /**
     * @return ISO639
     */
    public function getIso639()
    {
        return $this->iso639;
    }

    /**
     * @param mixed $iso639Define
     * @return L10NAdapter
     */
    public function setIso639Define($iso639Define)
    {
        $this->iso639Define = $iso639Define;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIso639Define()
    {
        return $this->iso639Define;
    }

    /**
     * @param mixed $language
     * @return L10NAdapter
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param ISO3166 $iso3166
     * @return L10NAdapter
     */
    public function setIso3166($iso3166)
    {
        $this->iso3166 = $iso3166;
        return $this;
    }

    /**
     * @return ISO3166
     */
    public function getIso3166()
    {
        return $this->iso3166;
    }

    /**
     * @param mixed $locations
     * @return L10NAdapter
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param array $locales
     * @return L10NAdapter
     */
    public function setLocales($locales)
    {
        $this->locales = $locales;
        return $this;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }


}
