<?php
/**
 * Controller 基类
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/4/27
 * Time: 18:20
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;


use Lephp\Helpers\ArrayHelper;
use Lephp\Helpers\Json;
use Lephp\Plugins\LogPlugin;
use Lephp\Validators\Validator;
use Yaf\Controller_Abstract;

class Controllers extends Controller_Abstract
{
    protected $_outputFormat;
    protected $_callback;
    protected $defaultInputParams;

    /**
     * 初始化项目
     */
    public function init()
    {
        /**
         * 设置模板目录为模块目录
         */
        $viewPath = APPLICATION_PATH . "/" . APP_NAME . "/modules/" . $this->getModuleName() . "/views/";
        $this->_view->setScriptPath($viewPath);
        if (!$this->hasMethod($this->getRequest()->action . 'action')) {
            header('Page Not Found', true, 404);
            throw new \Exception('Page Not Found', 404);
        }
    }

    /**
     * 初始化输入参数
     * @param $defaultInputParams
     * @param array $unsetList
     * @param array $extraParams
     * @return mixed
     */
    public function initInputParams($defaultInputParams = [], $unsetList = [], $extraParams = [])
    {
        if (empty($defaultInputParams) && is_array($defaultInputParams)) {
            $defaultInputParams = $this->defaultInputParams;
        }
        //取传进参数值，不存在取默认值
        foreach (array_keys($defaultInputParams) as $field) {
            $defaultInputParams[$field] = !is_null($this->getQuery($field)) ? $this->getQuery($field) : $defaultInputParams[$field];
        }
        //需要的额外参数值
        if (!empty($extraParams)) {
            $defaultInputParams = array_merge($defaultInputParams, $extraParams);
        }
        //unset掉为空的键
        if (!empty($unsetList)) {
            foreach ($unsetList as $unsetItem) {
                if (isset($defaultInputParams[$unsetItem])) {
                    if ('' === $defaultInputParams[$unsetItem]) {
                        unset($defaultInputParams[$unsetItem]);
                    }
                }
            }
        }
        $this->defaultInputParams = $defaultInputParams;
        return $this;
    }

    /**
     * 自动检查参数
     *
     * 参数定义格式
     * $arr['add_user'] = [[['number1', 'number2'], 'required','message' => '不能为空', 'exclude'=>true],
     *                    [['number2'], 'max' => 5, 'min' => 1, 'message' => '取值范围不合法']];
     *
     * message 支持 'message' => ['max' => '数值不能大于5', 'min' => '数值不能小于1']
     *
     * exclude 表示另外，当前情况下此参数不验证，传入bool值 支持 'exclude' => ['max' => true]
     *
     * 支持参数
     * required 是否为空
     * min 最小值 1
     * max 最大值 100
     * length 字符串长度 5
     * max_length 字符串最大长度 10
     * min_length 字符串最小长度 5
     * in 包含值 ['a', 'b', 'c']
     * date 是否为日期
     * time 是否为时间
     * mail 是否为邮箱
     * mailorphone 是否为手机号和邮箱
     * tel 是否为电话号
     * phone 是否为手机号
     * zip 是否为邮编
     * url 是否为url
     * numeric 是否为数字
     *
     * @return array
     */
    protected function validateRules()
    {
        return [];
    }

    /**
     * 在controller里面定义好$validateRules属性，
     * 然后在需要使用验证的action里面调用此方法
     * @param string $item
     * @param bool $throwException
     * @return bool|Controllers
     * @throws Exception
     */
    public function validate($item = __FUNCTION__, $throwException = false)
    {
        $validateRules = $this->validateRules();
        if (isset($validateRules[$item]) && is_array($validateRules[$item])) {
            foreach ($validateRules[$item] as $validateRule) {
                /** @var array | string $preValidateInputKeys 待校验参数名、列表 */
                $preValidateInputKeys = $validateRule[0];
                unset($validateRule[0]);
                $builtInValidators = Validator::$builtInValidators;
                foreach ($validateRule as $ruleName => $ruleDetail) {
                    if (isset($builtInValidators[$ruleName])) {
                        $validator = Validator::createValidator(
                            $ruleName,
                            $this,
                            $preValidateInputKeys,
                            $ruleDetail);
                        try {
                            $validator->validateInputs();
                        } catch (InvalidParamException $e) {
                            if ($throwException) {
                                throw new Exception($e->getMessage());
                            } else {
                                Output::send($e->getMessage(), 'json');
                                $this->die();
                            }

                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * 获取get参数
     *
     * @param $key
     * @param $default
     *
     * @access protected
     * @return mixed
     */
    protected function getQuery($key = null, $default = null)
    {
        return Input::getQuery($key, $default);
    }


    /**
     * 获取Post参数
     *
     * @param $key
     * @param $default
     *
     * @access protected
     * @return mixed
     */
    protected function getPost($key = null, $default = null)
    {
        return Input::getPost($key, $default);
    }

    /**
     * 获取cookie
     *
     * @param  $key
     * @param  $default
     *
     * @access protected
     * @return mixed
     */
    protected function getCookie($key = null, $default = null)
    {
        return Input::getCookie($key, $default);
    }

    /**
     * 获取文件域
     *
     * @param  $key
     *
     * @access protected
     * @return mixed
     */
    protected function getFile($key = null)
    {
        return Input::getFile($key);
    }


    /**
     * 返回当前控制器名
     *
     * @access public
     * @return string
     */
    public function getControllerName()
    {
        return $this->getRequest()->getControllerName();
    }

    /**
     * 返回当前动作名
     *
     * @access public
     * @return string
     */
    public function getActionName()
    {
        return $this->getRequest()->getActionName();
    }

    /**
     * @param $response
     * @param string $format
     * @param string $callback
     * @param bool $exit
     * @return $this
     */
    public function sendOutput($response, $format = '', $callback = '', $exit = false)
    {
        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json; charset=UTF-8');
        if ($response instanceof \Exception && !APP_DEBUG) {
            $response = Error::info('system error');
        }
        $response = ArrayHelper::toArray($response);
        if ('' !== $callback) {
            $this->_callback = $callback;
        }
        if( $format == 'jsonp' ) {
            Output::send($response,$format,$this->_callback);
        } else {
            $this->getResponse()->setBody(Json::encode($response));
        }
        if ($exit) {
            Output::send($response);
            LogPlugin::getInstance()->flush($this->_request, $this->_response);
            exit;
        }
        return $this;
    }

    /**
     * 设置跳转
     * @param string $url
     * @return boolean;
     */
    public function redirect($url)
    {
        $this->getResponse()->setRedirect($url);
        return true;
    }

    /**
     * die
     */
    public function die()
    {
        die();
    }

    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }
}