<?php
namespace X\Service\Action\Handler;
use X\Core\X;
use X\Service\Action\ActionGroup;
use X\Service\Action\ActionException;
/**
 * @method void run() {}
 */
abstract class ActionBase {
    /** @var ActionGroup */
    private $group = null;
    /** @var array */
    private $parameters = array();
    
    /** @param ActionGroup $group */
    public function __construct( ActionGroup $group, array $params=array() ) {
        $this->group = $group;
        $this->parameters = $params;
    }
    
    /** @return void */
    protected function init() {}
    
    /**
     * 根据名称获取动作参数
     * @param string $name 参数名称
     * @param mixed $default 默认值
     * @return mixed
     */
    protected function getParameter( $name, $default=null ) {
        return key_exists($name, $this->parameters) 
        ? $this->parameters[$name] 
        : $default;
    }
    
    /**
     * 执行动作
     * 如果"beforeRunAction()"返回false， 则停止运行，并返回false。
     * @param array $parameters 执行参数
     * @return boolean
     */
    public function execute() {
        X::system()->getEventManager()->trigger('service-action-action-start', $this);
        if ( false === $this->beforeRunAction() ) {
            return false;
        }
        $result = $this->doRunAction();
        $this->afterRunAction();
        X::system()->getEventManager()->trigger('service-action-action-end', $this);
        return $result;
    }
    
    /**
     * 将参数应用到动作处理方法然后执行
     * @param array $parameters
     * @return boolean|mixed
     */
    protected function doRunAction() {
        $handlerName = 'run';
        if ( !method_exists($this, $handlerName) || !is_callable(array($this, $handlerName)) ) {
            throw new ActionException("Can not find action handler \"run()\".");
        }
        
        $paramsToMethod = array();
        $class = new \ReflectionClass($this);
        $method = $class->getMethod($handlerName);
        
        $parameters = $this->parameters;
        $parameterInfos = $method->getParameters();
        foreach ( $parameterInfos as $parmInfo ) {
            /* @var $parmInfo \ReflectionParameter */
            $name = $parmInfo->getName();
            if ( isset($parameters[$name]) ) {
                $paramsToMethod[$name] = $parameters[$name];
            } else if ( $parmInfo->isOptional() && $parmInfo->isDefaultValueAvailable() ) {
                $paramsToMethod[$name] = $parmInfo->getDefaultValue();
            } else {
                throw new ActionException('Parameters to action handler is not available.');
            }
        }
        
        $handler = array($this, $handlerName);
        return \call_user_func_array($handler, $paramsToMethod);
    }
    
    /**
     * 动作执行之前处理， 如果返回false， 则不再执行该动作
     * @return void
     */
    protected function beforeRunAction(){
        return true;
    }
    
    /**
     * 动作执行之后处理，如果动作没有被执行， 则不执行。
     * @return void
     */
    protected function afterRunAction(){}
    
    /** @return \X\Service\Action\ActionGroup */
    protected function getGroup() {
        return $this->group;
    }
}