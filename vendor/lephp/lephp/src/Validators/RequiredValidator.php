<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/11/21
 * Time: 上午10:20
 */

namespace Lephp\Validators;

use Lephp\Core\InvalidParamException;

/**
 * Class RequiredValidator
 * @package Lephp\Validators
 *
 */
class RequiredValidator extends Validator
{
    public $message;

    /**
     * @var bool 严格模式，既不能不传，也不能是空字符串，也不能是 'null'
     */
    public $strict = true;

    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = "%s is required";
        }
    }

    /**
     * @param mixed $message
     * @return RequiredValidator
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    protected function validateValue($value)
    {
        if (true === $this->strict) {
            /**
             * 严格模式:
             * 1. 不能为 ''
             * 2. 不能为 null
             * 3. 不能为空数组
             * 4. trim 之后不能为 空字符串
             * 5. 不能为这种空数组
             * [
             * '' => ''
             * ]
             */
            return $this->_validateStrict($value) ? null : [$this->message, []];
        } else {
            return !is_null($value) ? null : [$this->message, []];
        }
    }

    /**
     * @param integer| string | array $inputData 待校验数据 - value
     * @param string $attribute 待校验数据 - key
     * @return bool
     * @throws InvalidParamException 抛出参数错误异常
     */
    protected function _validate($inputData, $attribute)
    {
        if (is_null($inputData)) {
            throw new InvalidParamException(sprintf($this->message, $attribute));
        } elseif (true === $this->strict) {
            /**
             * 严格模式:
             * 1. 不能为 ''
             * 2. 不能为 null
             * 3. 不能为空数组
             * 4. trim 之后不能为 空字符串
             * 5. 不能为这种空数组
             * [
             * '' => ''
             * ]
             */
            if (false === $this->_validateStrict($inputData)) {
                throw new InvalidParamException(sprintf($this->message, $attribute));
            } else {
                return true;
            }
        } else {
            /**
             * 非严格模式
             * !is_null($inputData) 即可
             */
            return true;
        }
    }

    /**
     * Strict Mode
     * @param $value
     * @return bool Not null returns true
     */
    private function _validateStrict($value)
    {
        if (
            (is_array($value) && 1 <= count($value) && !$this->_validateStrict($value[0]))
            ||
            (!is_array($value) && '' === trim($value)) // 空字符串
            ||
            is_null($value) // null
            ||
            (!is_array($value) && 'null' === strtolower(trim($value))) // 'null'
            ||
            (!is_array($value) && 0 === count(trim($value)))
        ) {
            return false;
        }
        return true;
    }
}