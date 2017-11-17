<?php
/**
 *
 */
namespace X\Core\Util;

/**
 * @property \X\Core\Util\ConfigurationFile $configuration
 */
abstract class Manager {
    /** @var Manager[] All manager instances*/
    protected static $managers = null;
    /** @var string config key in configuration array */
    protected $configurationKey = null;
    /** @var ConfigurationArray */
    private $configuration = null;
    
    /**
     * 获取Management的实例。
     * @return \X\Core\Util\Manager
     */
    public static function getManager() {
        $manager = get_called_class();
        if ( !isset(self::$managers[$manager]) ) {
            self::$managers[$manager] = new $manager();
        }
        
        return self::$managers[$manager];
    }
    
    /**
     * 将构造函数不公开， 以防止框架内存在第二个管理实例。
     * @return void
     */
    protected function __construct() {
        $this->init();
    }
    
    /**
     * 初始化该管理器
     * @return void
     */
    protected function init() {}
    
    /**
     * @var integer
     */
    private $status = self::STATUS_STOPED;
    
    /**
     * @var unknown
     */
    const STATUS_STOPED = 0;
    
    /**
     * @var unknown
     */
    const STATUS_RUNNING = 1;
    
    /**
     * @return number
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * 启动该管理器
     * @return void
     */
    public function start() {
        $this->status = self::STATUS_RUNNING;
    }
    
    /**
     * 结束该管理器
     * @return void
     */
    public function stop() {
        $this->status = self::STATUS_STOPED;
    }
    
    /**
     * 销毁当前管理器
     */
    public function destroy() {
        self::$managers[get_class($this)] = null;
        unset(self::$managers[get_class($this)]);
    }
    
    /**
     * @return \X\Core\Util\ConfigurationFile
     */
    public function getConfiguration() {
        if ( null === $this->configuration ) {
            $config = \X\Core\X::system()->getConfiguration()->get($this->configurationKey, array());
            $this->configuration = new ConfigurationArray();
            $this->configuration->setValues($config);
        }
        return $this->configuration;
    }
    
    /**
     * @param string $name
     * @throws Exception
     * @return \X\Core\Util\ConfigurationFile
     */
    public function __get( $name ) {
        if ( 'configuration' === $name ) {
            return $this->getConfiguration();
        }
        throw new Exception('Unable to access prototype "'.$name.'"');
    }
}