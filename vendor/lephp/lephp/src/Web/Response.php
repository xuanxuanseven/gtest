<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 16/9/26
 * Time: 下午8:14
 */

namespace Lephp\Web;


use Lephp\Core\InvalidConfigException;
use Lephp\Lephp;

class Response extends \Lephp\Core\Response
{
    const FORMAT_RAW = 'raw';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_JSONP = 'jsonp';
    const FORMAT_XML = 'xml';

    public $format = self::FORMAT_HTML;

    private $_cookies;

    /**
     * Returns the cookie collection.
     * Through the returned cookie collection, you add or remove cookies as follows,
     *
     * ```php
     * // add a cookie
     * $response->cookies->add(new Cookie([
     *     'name' => $name,
     *     'value' => $value,
     * ]);
     *
     * // remove a cookie
     * $response->cookies->remove('name');
     * // alternatively
     * unset($response->cookies['name']);
     * ```
     *
     * @return CookieCollection the cookie collection.
     */
    public function getCookies()
    {
        if ($this->_cookies === null) {
            $this->_cookies = new CookieCollection;
        }
        return $this->_cookies;
    }

    protected function sendCookies(){
        if ($this->_cookies === null) {
            return;
        }
        $request = Lephp::$app->getRequest();
        if ($request->enableCookieValidation) {
            if ($request->cookieValidationKey == '') {
                throw new InvalidConfigException(get_class($request) . '::cookieValidationKey must be configured with a secret key.');
            }
            $validationKey = $request->cookieValidationKey;
        }
        foreach ($this->getCookies() as $cookie) {
            $value = $cookie->value;
            if ($cookie->expire != 1  && isset($validationKey)) {
                $value = Lephp::$app->getSecurity()->hashData(serialize([$cookie->name, $value]), $validationKey);
            }
            setcookie($cookie->name, $value, $cookie->expire, $cookie->path, $cookie->domain, $cookie->secure, $cookie->httpOnly);
        }
    }
}