<?php
namespace X\Core\Module;
use X\Core\X;
use X\Core\Component\Exception;
use X\Core\Component\Manager as UtilManager;
/**
 * 模块管理器
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Manager extends UtilManager {
    /** 
     * 管理器配置键名
     * @var string 
     * */
    protected $configurationKey = 'modules';
    
    /**
     * 默认模块名称
     * @var string
     * */
    protected $defaultModuleName = null;
    
    /** 
     * 已加载模块列表
     * @var array
     * */
    private $loadedModules = array();
    
    /**
     * 启动模块管理器
     * @see \X\Core\Component\Manager::start()
     * @return void
     */
    public function start() {
        parent::start();
        
        /* setup default module name */
        foreach ( $this->getConfiguration() as $name => $config ) {
            if ( isset($config['default']) && true===$config['default'] ) {
                $this->defaultModuleName = $name;
            }
            $this->load($name);
        }
    }
    
    /**
     * 停止模块管理器
     * @see \X\Core\Component\Manager::stop()
     */
    public function stop() {
        $this->loadedModules = array();
        $this->defaultModuleName = null;
        parent::stop();
    }
    
    /**
     * 运行模块, 并返回运行结果
     * @param string $name 模块名称
     * @throws Exception
     * @return mixed
     */
    public function run( $name=null ) {
        $parameters = X::system()->getParameter()->toArray();
        $moduleName = isset($parameters['module']) ? $parameters['module'] : $this->defaultModuleName;
        $moduleName = (null === $name) ? $moduleName : $name;
        if ( null === $moduleName ) {
            throw new Exception('Can not find any module to execute.');
        }
        $moduleName = ucfirst($moduleName);
        unset($parameters['module']);
        
        if ( !$this->isEnabled($moduleName) ) {
            throw new Exception("module `{$moduleName}` is disabled.");
        }
        $module = $this->load($moduleName);
        return $module->run($parameters);
    }
    
    /**
     * 根据模块名称加载模块
     * @param string $name 模块名称
     * @throws Exception
     * @return XModule
     */
    protected function load($name) {
        if ( !$this->has($name) ) {
            throw new Exception("Module '$name' can not be found.");
        }
        
        if ( isset($this->loadedModules[$name]) ) {
            return $this->loadedModules[$name];
        }
        
        $moduleClass = 'X\\Module\\'.$name.'\\Module';
        if ( !class_exists($moduleClass) ) {
            throw new Exception("Module handler '$moduleClass' can not be found.");
        }
        if ( !is_subclass_of($moduleClass, '\\X\\Core\\Module\\XModule') ) {
            throw new Exception("Module '$name' is not a available module.");
        }
        
        $config = $this->getConfiguration()->get($name);
        $module = new $moduleClass($config['params']);
        $this->loadedModules[$name] = $module;
        return $module;
    }
    
    /**
     * 判断模块是否启动
     * @param string $name 模块名称 
     * @return boolean 
     * */
    public function isEnabled( $name ) {
        $config = $this->getConfiguration()->get($name);
        return isset($config['enable']) ? $config['enable'] : false;
    }
    
    /**
     * 获取所有模块名称
     * @return array
     */
    public function getList() {
        return array_keys($this->getConfiguration()->toArray());
    }
    
    /**
     * Get All Modules
     * @return XModule[]
     */
    public function getAllModules() {
        $names = $this->getList();
        
        $modules = array();
        foreach ( $names as $name ) {
            $modules[$name] = $this->load($name);
        }
        return $modules;
    }
    
    /**
     * 判断模块是否存在
     * @param string $name 模块名称
     * @return boolean
     */
    public function has( $name ) {
        return $this->getConfiguration()->has($name);
    }
    
    /**
     * 获取模块实例
     * @param string $name 模块名称
     * @return XModule
     */
    public function get( $name ) {
        return $this->load($name);
    }
}