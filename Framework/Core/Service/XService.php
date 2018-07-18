<?php
namespace X\Core\Service;
use X\Core\X;
use X\Core\Component\ConfigurationArray;
use X\Core\Component\ClassHelper;
/**
 * 服务基础类
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class XService {
    /** 
     * 服务实例化缓存
     * @var \X\Core\Service\XService[]
     * */
    private static $services = array();
    
    /** 
     * 服务名称
     * @var string
     * */
    protected static $serviceName = null;
    
    /** 
     * 当前服务配置
     * @var ConfigurationArray 
     * */
    private $configuration = null;
    
    /**
     * 获取该服务的实例
     * @return self
     */
    static public function getService() {
        $manager =  X::system()->getServiceManager();
        $className = get_called_class();
        $serviceName = self::getServiceName();
        return $manager->get($serviceName);
    }
    
    /**
     * 从服务的类名中获取服务名称。
     * @param string $className 要获取服务名称的类名
     * @return string
     */
    private static function getServiceNameFromClassName( $className ) {
        $name = static::$serviceName;
        if ( null === $name ) {
            $className = explode('\\', $className);
            $name = $className[count($className)-2];
        }
        return $name;
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
     * 实例化服务
     * @return void
     */
    public function __construct( $config=array() ) {
        foreach ( $config as $confKey => $confVal ) {
            if ( property_exists($this, $confKey) ) {
                $this->{$confKey} = $confVal;
            }
        }
        
        # @TODO : remove the following stuff, use the property instead.
        $this->configuration = new ConfigurationArray();
        $this->configuration->setValues($config);
        
        $this->onLoaded();
    }
    
    /**
     * 获取当前服务配置
     * @return \X\Core\Component\ConfigurationArray
     */
    public function getConfiguration() {
        return $this->configuration;
    }
    
    /**
     * 服务加载时调用
     * @return void
     */
    protected function onLoaded() {
        return;
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
     * 销毁当前服务
     * @return void
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
        return ClassHelper::getPathRelatedClass($this, $path);
    }
    
    
    /**
     * 服务运行状态 : 已停止
     * @var integer
     */
    const STATUS_STOPPED = 0;
    
    /**
     * 服务运行状态 : 运行中
     * @var integer
     */
    const STATUS_RUNNING = 1;
    
    /**
     * 当前服务状态
     * @var integer
     */
    private $status = self::STATUS_STOPPED;
    
    /**
     * 获取当前服务状态
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * @return string
     */
    public static function getClassName() {
        return get_called_class();
    }
}