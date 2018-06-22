<?php
namespace X\Core;
/**
 * 框架主类
 * @author Michael Luthor <michaelluthor@163.com>
 */
class X {
    /**
     * 框架实例
     * @var X
     */
    private static $system = null;
    /**
     * 是否为第一次启动
     * @var boolean
     */
    private static $isFirstStart = null;
    /**
     * 配置文件路径
     * @var string
     */
    private $configPath = null;
    /**
     * 应用根目录
     * @var string
     */
    private $root = null;
    
    /** @var string framework root path. */
    private $frameworkRoot = null;
    
    /** 
     * 运行环境管理器
     * @var \X\Core\Environment\Environment
     * */
    private $environment = null;
    
    /** 
     * 参数管理器
     * @var \X\Core\Component\ConfigurationArray
     * */
    private $parameters = array();
    
    /** 
     * 配置管理器
     * @var \X\Core\Component\ConfigurationFile 
     * */
    private $configuration = null;
    
    /** 
     * 事件管理器
     * @var \X\Core\Event\Manager 
     * */
    private $eventManager = null;
    
    /** 
     * 服务管理器
     * @var \X\Core\Service\Manager
     * */
    private $serviceManager = null;
    
    /** 
     * 模块管理器
     * @var \X\Core\Module\Manager 
     * */
    private $moduleManager = null;
    
    /**
     * 动该框架。
     * @return X
     */
    public static function start( $configPath ){
        if ( null === self::$system ) {
            self::$isFirstStart = ( null === self::$isFirstStart ) ? true : false;
            self::$system = new X($configPath);
        }
        return self::$system;
    }
    
    /**
     * @return boolean
     */
    public static function isRunning() {
        return null !== self::$system;
    }
    
    /**
     * 构造函数， 初始化框架的环境。
     * @return void
     */
    private function __construct($configPath) {
        $this->configPath = $configPath;
        $config = require $configPath;
        
        spl_autoload_register(array($this, '_autoloader'));
        $this->root = $config['document_root'];
        $this->frameworkRoot = dirname(__DIR__);
        $this->environment = new \X\Core\Environment\Environment();
        $this->environment->init();
        
        $this->parameters = new \X\Core\Component\ConfigurationArray();
        $this->parameters->merge($this->environment->getParameters());
        $this->configuration = new \X\Core\Component\ConfigurationArray();
        if ( isset($config['params']) ) {
            $params = new \X\Core\Component\ConfigurationArray();
            $params->setValues($config['params']);
            $config['params'] = $params;
        }
        $this->configuration->setValues($config);
        
        $this->eventManager = \X\Core\Event\Manager::getManager();
        
        $this->eventManager->start();
        if ( self::$isFirstStart ) {
            register_shutdown_function(array($this, '_shutdown'));
        }
    }
    
    /**
     * 获取事件管理器
     * @return \X\Core\Event\Manager
     */
    public function getEventManager() {
        return $this->eventManager;
    }
    
    /**
     * 获取参数
     * @return \X\Core\Component\ConfigurationArray
     */
    public function getParameter() {
        return $this->parameters;
    }
    
    /**
     * 获取运行环境
     * @return \X\Core\Environment\Environment
     */
    public function getEnvironment() {
        return $this->environment;
    }
    
    /**
     * 获取当前框架的配置信息。
     * @return \X\Core\Component\ConfigurationFile
     */
    public function getConfiguration() {
        return $this->configuration;
    }
    
    /**
     * @return string
     */
    public function getConfigurationPath() {
        return $this->configPath;
    }
    
    /**
     * 获取当前框架种的service manager的实例。
     * @return \X\Core\Service\Manager
     */
    public function getServiceManager() {
        if ( null === $this->serviceManager ) {
            $this->serviceManager = \X\Core\Service\Manager::getManager();
        }
        return $this->serviceManager;
    }
     
    /**
     * 返回当前框架中的实例。
     * @return \X\Core\Module\Manager
     */
    public function getModuleManager() {
        if ( null === $this->moduleManager ) {
            $this->moduleManager = \X\Core\Module\Manager::getManager();
        }
        return $this->moduleManager;
    }
    
    /**
     * 根据提供的字符串返回适合当前操作系统的路径字符串。 如果提供的路径为空， 则返回该项目的
     * 根目录路径。路径字符串的目录分割符为'/'。
     * 注意，该方法用于生成路径而没有限制生成后的路径必须在框架根目录下，换句话说就是，该方法
     * 生成的路径可以是任何地方。甚至是"/etc/passwd"等敏感路径。
     *
     * @param string $path 路径字符串。
     * @return string 适合当前操作系统的路径字符串。
     */
    public function getPath( $path='' ) {
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $path = empty($path) ? $this->root : $this->root.DIRECTORY_SEPARATOR.$path;
        return $path;
    }
    
    /**
     * 结束框架的运行。
     * @param string $exit 是否结束执行脚本。
     */
    public function stop( $exit=true ) {
        $exit ? exit() : $this->_shutdown();
    }
    
    /**
     * 获取已经启动的框架实例， 如果框架没有启动， 则会抛出异常。
     * @return \X\Core\X
     */
    public static function system() {
        if ( null === self::$system ) {
            throw new \X\Core\Component\Exception('X has not been started.');
        }
        return self::$system;
    }
    
    /**
     * 该方法是当脚本结束或异常停止时所调用的方法， 完成该次请求的结尾工作。
     * 该方法由PHP内核调用， 不建议在代码中直接调用该方法。
     * @return void
     */
    public function _shutdown() {
        /* 如果框架已经停止， 则不再执行该方法。 当手动结束框架但未退出时， 这种情况就会发生。 */
        if ( null === self::$system ) {
            return;
        }
        
        $this->getModuleManager()->stop();
        $this->getModuleManager()->destroy();
        $this->getServiceManager()->stop();
        $this->getServiceManager()->destroy();
        $this->eventManager->stop();
        self::$system = null;
        spl_autoload_unregister(array($this, '_autoloader'));
    }
    
    /**
     * 该方法用于实现类的按需加载， 加载方式根据需要加载的类的名称以及其所在的命名空间
     * 进行拼接处理， 所以类的存放位置应当与其命名空间相对应。
     * 该方法由PHP内核调用， 不建议在代码中直接调用该方法。
     * @param string $class 需要动态加载的类的完整名称
     * @return void
     */
    public function _autoloader( $class ) {
        $path = explode('\\', $class);
        if ( 1 === count($path) ) {
            return;
        }
        $basePaths = array();
        if ( isset($path[1]) && 'Core' === $path[1] ) {
            $basePaths[] = dirname(dirname(__FILE__));
        } else if ( 'Module' === $path[1] ) {
            unset($path[1]);
            $basePaths = $this->getConfiguration()->get('module_path');
            $basePaths[] = $this->frameworkRoot.'/Module';
            $basePaths[] = $this->getConfiguration()->get('document_root').'/Module';
        } else if ( 'Service' === $path[1] ) {
            unset($path[1]);
            $basePaths = $this->getConfiguration()->get('service_path');
            $basePaths[] = $this->frameworkRoot.'/Service';
            $basePaths[] = $this->getConfiguration()->get('document_root').'/Service';
        } else if ( 'Library' === $path[1] ) {
            unset($path[1]);
            $basePaths = $this->getConfiguration()->get('library_path', array());
            $basePaths[] = $this->root."/Library";
            $basePaths[] = $this->getConfiguration()->get('document_root').'/Library';
        }
        
        $basePaths[] = $this->root;
        foreach ( $basePaths as $basePath ) {
            $classPath = $path;
            $classPath[0] = rtrim(rtrim($basePath, DIRECTORY_SEPARATOR), "/");
            $classPath = implode(DIRECTORY_SEPARATOR, $classPath).'.php';
            if ( is_file($classPath) ) {
                require $classPath;
                break;
            }
        }
    }
    
    /**
     * 运行框架。一般在start之后调用。
     * 如果需要进行其他配置， 则必须在run之前进行。
     * @return void
     */
    public function run() {
        $this->getServiceManager()->start();
        $this->getModuleManager()->start();
        $this->getModuleManager()->run();
    }
}