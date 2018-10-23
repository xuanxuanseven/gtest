<?php
/**
 * Created by PhpStorm.
 * User: marlin
 * Date: 2016/12/22
 * Time: 上午11:56
 */

namespace Lephp\Core;

/**
 * ModelEvent represents the parameter needed by [[YModel]] events.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ModelEvent extends Event
{
    /**
     * @var bool whether the model is in valid status. Defaults to true.
     * A model is in valid status if it passes validations or certain checks.
     */
    public $isValid = true;
}