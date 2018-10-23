<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/22
 * Time: 下午2:24
 */

namespace Lephp\Validators;


use Lephp\Core\InvalidConfigException;
use Lephp\Helpers\ArrayHelper;

class RangeValidator extends Validator
{
    public $range;

    public $strict = false;

    public $not = false;

    public $allowArray = false;


    public function init()
    {
        parent::init();
        if (!is_array($this->range)
            && !($this->range instanceof \Closure)
            && !($this->range instanceof \Traversable)
        ) {
            throw new InvalidConfigException('The "range" property must be set.');
        }

        if ($this->message === null) {
            $this->message = '%s is invalid';
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $in = false;
        if ($this->allowArray && ($value instanceof \Traversable || is_array($value))
            && ArrayHelper::isSubset($value, $this->range, $this->strict)
        ) {
            $in = true;
        }
        if (!$in && ArrayHelper::isIn($value, $this->range, $this->strict)) {
            $in = true;
        }
        return $this->not !== $in ? null : [$this->message, []];
    }
}