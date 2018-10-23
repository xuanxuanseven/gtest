<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/28
 * Time: 上午10:25
 */

namespace Lephp\Validators;


class NumberValidator extends Validator
{
    /**
     * @var bool whether the attribute value can only be an integer. Defaults to false.
     */
    public $integerOnly = false;
    /**
     * @var int|float upper limit of the number. Defaults to null, meaning no upper limit.
     * @see tooBig for the customized message used when the number is too big.
     */
    public $max;
    /**
     * @var int|float lower limit of the number. Defaults to null, meaning no lower limit.
     * @see tooSmall for the customized message used when the number is too small.
     */
    public $min;
    /**
     * @var string user-defined error message used when the value is bigger than [[max]].
     */
    public $tooBig;
    /**
     * @var string user-defined error message used when the value is smaller than [[min]].
     */
    public $tooSmall;
    /**
     * @var string the regular expression for matching integers.
     */
    public $integerPattern = '/^\s*[+-]?\d+\s*$/';
    /**
     * @var string the regular expression for matching numbers. It defaults to a pattern
     * that matches floating numbers with optional exponential part (e.g. -1.23e-10).
     */
    public $numberPattern = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';


    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = $this->integerOnly ? '%s must be an integer.' : '%s must be a number.';
        }
        if ($this->min !== null && $this->tooSmall === null) {
            $this->tooSmall = '%s must be no less than %s.';
        }
        if ($this->max !== null && $this->tooBig === null) {
            $this->tooBig = '%s must be no greater than %s.';
        }
    }

    protected function validateValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return ['%s is invalid.', []];
        }
//        if (is_null($value)) {
//            return $value; //gun ni ma de
//        }
        $pattern = $this->integerOnly ? $this->integerPattern : $this->numberPattern;
        if (!preg_match($pattern, "$value")) {
            return [$this->message, []];
        } elseif ($this->min !== null && $value < $this->min) {
            return [$this->tooSmall, ['min' => $this->min]];
        } elseif ($this->max !== null && $value > $this->max) {
            return [$this->tooBig, ['max' => $this->max]];
        } else {
            return null;
        }
    }
}