<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/11/15
 * Time: 下午4:07
 */

namespace Lephp\Validators;


use Lephp\Core\Component;
use Lephp\Core\Input;
use Lephp\Core\InvalidParamException;
use Lephp\Lephp;

/**
 * Class Validator
 * @package Lephp\Validators
 *
 */
class Validator extends Component
{
    public $message;
    /**
     * @var array 内构的类型检查器 ( name => class or configuration)
     */
    public static $builtInValidators = [
        'file'                 => 'Lephp\Validators\FileValidator',
        'required'             => 'Lephp\Validators\RequiredValidator',
        'in'                   => 'Lephp\Validators\RangeValidator',
        'number'               => 'Lephp\Validators\NumberValidator',
        'string'               => 'Lephp\Validators\StringValidator',
        'regex'                => 'Lephp\Validators\RegularExpressionValidator',
        'compare'              => 'Lephp\Validators\CompareValidator',
        'min'                  => [
            'class'       => 'Lephp\Validators\NumberValidator',
            'integerOnly' => true
        ],
        'max'                  => [
            'class'       => 'Lephp\Validators\NumberValidator',
            'integerOnly' => true
        ],
        'equals'               => 'Lephp\Validators\CompareValidator',
        'greaterThan'          => 'Lephp\Validators\CompareValidator',
        'lessThan'             => 'Lephp\Validators\CompareValidator',
        'greaterThanOrEqualTo' => 'Lephp\Validators\CompareValidator',
        'lessThanOrEqualTo'    => 'Lephp\Validators\CompareValidator',
        'notEquals'            => 'Lephp\Validators\CompareValidator'
    ];

    /**
     * @var array | string 需要执行类型检查的参数列表
     */
    public $attributes = [];

    public function init()
    {
        parent::init();
        if (!is_array($this->attributes))
            $this->attributes = (array)$this->attributes;
    }

    /**
     * 初始化一个类型检查器
     * @param $type
     * @param $object \Lephp\Core\Controllers
     * @param $attributes
     * @param array $params 用于创建类型检查器的参数
     * @return object|Validator|FileValidator
     */
    public static function createValidator($type, $object, $attributes, $params = [])
    {
        $params['attributes'] = $attributes;
        if ($type instanceof \Closure || $object->hasMethod($type)) {
            // 类属性值 类型检查器
            $params['class'] = __NAMESPACE__ . '\InlineValidator';
            $params['method'] = $type;
        } else {
            if (isset(self::$builtInValidators[$type])) {
                $type = static::$builtInValidators[$type];
            }
            if (is_array($type)) {
                foreach ($type as $name => $value) {
                    $params[$name] = $value;
                }
            } else {
                $params['class'] = $type;
            }
        }
        return Lephp::createObject($params);
    }

    public function validate()
    {
        foreach ($this->attributes as $attribute) {
            $inputData = Input::getQuery($attribute);
            $this->_validate($inputData, $attribute);
        }
    }

    /**
     * 校验 input
     */
    public function validateInputs()
    {
        foreach ($this->attributes as $attribute) {
            if (!($this instanceof RequiredValidator) && is_null(Input::getQuery($attribute))) {
                // 如果规则不是 required，并且未能取到本值，则跳过下面的步骤
                continue;
            }
            $result = $this->validateValue(Input::getQuery($attribute));
            if (!is_null($result)) {
                throw new InvalidParamException(sprintf($result[0], $attribute));
            }
        }
        return true;
    }

    public function validateAttributes()
    {
        $this->_validateAttribute($this->attributes);
    }

    /**
     * @param $inputData
     * @return bool
     */
    protected function _validate($inputData, $attribute)
    {
        return true;
    }

    protected function _validateAttribute($attribute)
    {
        return true;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateAttribute($value)
    {
        return true;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateValue($value)
    {
        return true;
    }

}