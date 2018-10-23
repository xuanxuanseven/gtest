<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 6/13/16
 * Time: 4:09 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;


use HttpException;

class ExternalInterface extends Component
{
    protected $apiKey;
    protected $apiId;
    protected $businessId;

    protected $requestUrl;
    protected $requestParams;

    protected $response;
    protected $responseDecoded;


    protected $valueKey = 'data';

    /**
     * 生成签名字符串
     * @param $apiKey
     * @param $apiID
     * @param $data
     * @param string $signKeyName
     * @param string $signType
     * @return ExternalInterface
     */
    public function generateSign($apiKey, $apiID, $data, $signKeyName = 'sign', $signType = 'md5')
    {
        switch ($signType) {
            case 'md5':
                $md5 = md5(join('', $data) . $apiKey);
                $this->requestParams[$signKeyName] = $md5;
                break;
            default:
                $md5 = md5(join('', $data) . $apiKey);
                $this->requestParams[$signKeyName] = $md5;
                break;
        }
        return $this;
    }

    /**
     * 初始化请求参数
     * @param array $defaultRequestParams
     * @param array $requestParams
     * @param array $unsetList
     * @param array $extraParams
     * @return $this
     */
    public function initRequestParams($defaultRequestParams = [], $requestParams = [], $unsetList = [], $extraParams = [])
    {
        //取传进参数值，不存在取默认值
        foreach (array_keys($defaultRequestParams) as $field) {
            $defaultRequestParams[$field] = isset($requestParams[$field]) ? $requestParams[$field] : $defaultRequestParams[$field];
        }
        //需要的额外参数值
        if (!empty($extraParams)) {
            $defaultRequestParams = array_merge($defaultRequestParams, $extraParams);
        }
        //unset掉为空的键
        if (!empty($unsetList)) {
            foreach ($unsetList as $unsetItem) {
                if (isset($defaultRequestParams[$unsetItem])) {
                    if ('' === $defaultRequestParams[$unsetItem]) {
                        unset($defaultRequestParams[$unsetItem]);
                    }
                }
            }
        }
        $this->setRequestParams($defaultRequestParams);
        return $this;
    }

    public function request()
    {
        $http = new Http();
        try {

            $http_query = empty($this->requestParams) ? '' : '?' . http_build_query($this->requestParams);
            $api_url = $this->requestUrl . $http_query;
            $this->setResponse($http->request($api_url));
            $this->setResponseDecoded(json_decode($this->getResponse(), true));
            return $this;
        } catch (HttpException $e) {
            //TODO Log Warning
            throw $e;
        }
    }

    public function multiRequest($url, $params, $request = 'get')
    {
        $httpMulti = new HttpMulti();
        foreach ($params as $item => $value) {
            $httpMulti->add($url, $value, $request, $item);
        }
        try {
            $this->setResponse($httpMulti->exec());
            $responseDecoded = [];
            foreach ($this->getResponse() as $item => $value) {
                $responseDecoded[$item] = json_decode($value, true);
            }
            $this->setResponseDecoded($responseDecoded);
            return $this;
        } catch (HttpException $e) {
            throw $e;
        }
    }

    public function postRequest()
    {
        $http = new Http();
        try {
            $this->setResponse($http->curlPost($this->requestUrl, $this->requestParams));
            $this->setResponseDecoded(json_decode($this->getResponse(), true));
            return $this;
        } catch (HttpException $e) {
            //TODO Log Warning
            throw $e;
        }
    }

    public function get()
    {
        $this->setResponseDecoded([]);
        $this->setResponse('');
        return $this->request();
    }

    public function getMulti($url, $params)
    {
        $this->setResponseDecoded([]);
        $this->setResponse('');
        $this->multiRequest($url, $params);
    }

    public function post()
    {
        $this->setResponseDecoded([]);
        $this->setResponse('');
        return $this->postRequest();
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     * @return \Lephp\Core\ExternalInterface
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiId()
    {
        return $this->apiId;
    }

    /**
     * @param mixed $apiId
     * @return \Lephp\Core\ExternalInterface
     */
    public function setApiId($apiId)
    {
        $this->apiId = $apiId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBusinessId()
    {
        return $this->businessId;
    }

    /**
     * @param mixed $businessId
     * @return \Lephp\Core\ExternalInterface
     */
    public function setBusinessId($businessId)
    {
        $this->businessId = $businessId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     * @return \Lephp\Core\ExternalInterface
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponseDecoded()
    {
        return $this->responseDecoded;
    }

    /**
     * @param mixed $responseDecoded
     * @return \Lephp\Core\ExternalInterface
     */
    public function setResponseDecoded($responseDecoded)
    {
        $this->responseDecoded = $responseDecoded;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * @param mixed $requestParams
     * @return \Lephp\Core\ExternalInterface
     */
    public function setRequestParams($requestParams)
    {
        $this->requestParams = $requestParams;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

    /**
     * @param mixed $requestUrl
     * @return \Lephp\Core\ExternalInterface
     */
    public function setRequestUrl($requestUrl)
    {
        $this->requestUrl = $requestUrl;
        return $this;
    }

    /**
     * @param string $valueKey
     * @return $this
     */
    public function setValueKey($valueKey)
    {
        $this->valueKey = $valueKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getValueKey()
    {
        return $this->valueKey;
    }
}