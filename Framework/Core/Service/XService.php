<?php
namespace X\Core\Service;
use X\Core\X;
use X\Core\Util\XUtil;
use X\Core\Util\ConfigurationArray;
abstract class XService {
    /** @var \X\Core\Service\XService[] all service instances. */
    private static $services = array();
    /** @var string serive name */
    protected static $serviceName = null;
    /** @var ConfigurationArray */
    private $configuration = null;
    
    /**
     * 获取该服务的实例, 如果配置不为空，则创建该服务。
     * @return self
     */
    static public function getService($config=null) {
        $manager =  X::system()->getServiceManager();
        $className = get_called_class();
        $serviceName = self::getServiceName();
    
        if ( $manager->isLoaded($serviceName) ) {
            return $manager->get($serviceName);
        }
    
        if ( null !== $config ) {
            self::$services[$className] = new $className($config);
            return self::$services[$className];
        } else {
            return $manager->get($serviceName);
        }
    }
    
    /**
     * 从服务的类名中获取服务名称。
     * @param string $className 要获取服务名称的类名
     * @return string
     */
    private static function getServiceNameFromClassName( $className ) {
        return static::$serviceName;
    }
    
    /**
     * 静态方法获取服务名称
     * @return string
     */
    public static function getServiceName() {
        return self::getServiceNameFromClassName(get_called_class());
    }
    
    /**
     * 非静态方法获取服务名称
     * @return string
     */
    public function getName() {
        return self::getServiceNameFromClassName(get_class($this));
    }
    
    /**
     * 将构造函数保护起来以禁止从其他地方实例化服务。
     * @return void
     */
    protected function __construct( $config=array() ) {
        $this->configuration = new ConfigurationArray();
        $this->configuration->setValues($config);
        
        $this->onLoaded();
    }
    
    /**
     * @return \X\Core\Util\ConfigurationArray
     */
    public function getConfiguration() {
        return $this->configuration;
    }
    
    /**
     * @return NULL
     */
    protected function onLoaded() {
        return null;
    }
    
    /**
     * 启动服务，该方法由管理器启动， 不建议在其他地方调用该方法。
     * @return void
     */
    public function start(){
        $this->status = self::STATUS_RUNNING;
    }
    
    /**
     * 结束服务，该方法由管理器结束， 不建议在其他地方调用该方法。
     *  @return void
     */
    public function stop(){ 
        $this->status = self::STATUS_STOPPED;
    }
    
    /**
     * 
     */
    public function destroy() {
        $className = get_called_class();
        unset(self::$services[$className]);
    }
    
    /**
     * 获取当前服务下的文件或目录的绝对路径。
     * @return string
     */
    public function getPath( $path=null ) {
        return XUtil::getPathRelatedClass($this, $path);
    }
    
    
    /**
     * @var integer
     */
    const STATUS_STOPPED = 0;
    
    /**
     * @var integer
     */
    const STATUS_RUNNING = 1;
    
    /**
     * @var status
     */
    private $status = self::STATUS_STOPPED;
    
    /**
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * @return string
     */
    public function getPrettyName(){
        return $this->getName();
    }
    
    /**
     * @return string
     */
    public function getDescription(){
        return '';
    }
    
    /**
     * @return multitype:number
     */
    public function getVersion() {
        return array(0,0,0);
    }
    
    /**
     * @return string
     */
    public static function getClassName() {
        return get_called_class();
    }
}