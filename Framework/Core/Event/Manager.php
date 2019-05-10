<?php
namespace X\Core\Event;
use X\Core\Component\Manager as UtilManager;
/**
 * 事件管理器
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Manager extends UtilManager {
    /**
     * 服务配置键名
     * @var string
     * */
    protected $configurationKey = 'events';
    
    /**
     * 已经注册的事件处理器列表
     * @var array
     */
    private $eventHandlers = array(
        /* 'event-nane' => array('event', 'handler') */
    );
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Component\Manager::start()
     */
    public function start() {
        parent::start();
        
        $eventConfig = $this->getConfiguration();
        foreach ( $eventConfig as $eventName => $handlers ) {
            foreach ( $handlers as $handler ) {
                $this->registerHandler($eventName, $handler);
            }
        }
        return true;
    }
    
    /**
     * 注册事件处理器
     * @param string $eventName 事件名称
     * @param callable $eventHandler 事件处理器
     * @return void
     */
    public function registerHandler( $eventName, $eventHandler ) {
        if ( !isset($this->eventHandlers[$eventName]) ) {
            $this->eventHandlers[$eventName] = array();
        }
        $this->eventHandlers[$eventName][] = $eventHandler;
    }
    
    /**
     * 触发事件， 并返回每个事件处理器的处理结果数组
     * @param string $eventName 事件名称
     * @param mixed $param1 触发参数1
     * @param mixed $param2,... 触发参数2....
     * @return array
     */
    public function trigger( $eventName ) {
        if ( !isset($this->eventHandlers[$eventName]) ) {
            return array();
        }
        
        $parameters = func_get_args();
        array_shift($parameters);
        
        $result = array();
        foreach ( $this->eventHandlers[$eventName] as $handler ) {
            if ( is_callable($handler) ) {
                $result[] = call_user_func_array($handler, $parameters);
            } else {
                $result[] = $handler;
            }
        }
        return $result;
    }
}