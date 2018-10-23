<?php
namespace Lephp\Plugins\Smarty;

use Exception;
use Lephp\Core\Component;
use Smarty;
use Yaf\Application;
use Yaf\View_Interface;

/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 6/16/16
 * Time: 10:31 AM
 * @copyright LeEco
 * @since 1.0.0
 */
class SmartyAdapter extends Component implements View_Interface
{
    /**
     * smarty实例
     * @var Smarty $_smarty
     */
    public $_smarty;


    /**
     * 初始化smarty模板
     * Smarty_Adapter constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
//        parent::__construct();
        $this->_smarty = new Smarty();
        $this->_smarty->addPluginsDir([__DIR__ . '/plugins']);
        $smartyConfig = Application::app()
            ->getConfig()
            ->smarty
            ->toArray();
        foreach ($smartyConfig as $key => $value) {
            $this->_smarty->$key = $value;
        }
        $this->_smarty->cache_lifetime = 0;
        $this->_smarty->caching = false;
    }

    /**
     * (Yaf >= 2.2.9)
     * 传递变量到模板
     *
     * 当只有一个参数时，参数必须是Array类型，可以展开多个模板变量
     *
     * @param string $name 变量名
     * @param string $value 变量值
     *
     * @return Boolean
     */
    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->_smarty->assign($name);
            return;
        }
        $this->_smarty->assign($name, $value);
    }

    /**
     * 渲染模板并返回结果
     *
     * @param string $name 模板文件名
     * @param array $tpl_vars 模板变量
     *
     * @return String
     */
    public function render($name, $tpl_vars = [])
    {
        return $this->_smarty->fetch($name);
    }

    /**
     * 渲染模板并直接输出
     *
     * @param string $name 模板文件名
     * @param array $tpl_vars 模板变量
     *
     * @return Boolean
     */
    public function display($name, $tpl_vars = [])
    {
        if (!empty($tpl_vars)) {
            $this->assign($tpl_vars);
        }
        $this->_smarty->display($name);
    }

    /**
     * 设置模板文件目录
     *
     * @param string $tpl_dir 模板文件目录路径
     * @throws Exception
     * @return boolean
     */
    public function setScriptPath($tpl_dir)
    {
        if (is_readable($tpl_dir)) {
            $this->_smarty->template_dir = $tpl_dir;
        } else {
            throw new Exception('Invalid path provided');
        }
    }

    /**
     * 获取模板目录文件
     *
     * @return String
     */
    public function getScriptPath()
    {
        return $this->_smarty->template_dir;
    }
}