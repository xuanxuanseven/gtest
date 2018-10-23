<?php
namespace Lephp\Web;


/**
 * Lephp Application For Web Service
 *
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 6/13/16
 * Time: 11:33 PM
 * @copyright LeEco
 * @since 1.1.0
 */
class Application extends \Lephp\Core\Application
{
    protected function bootstrap()
    {

    }

    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
        ]);
    }
}