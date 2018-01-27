<?php
namespace X\Service\XAction\Util;
/**
 * 动作基础类
 * @author Michael Luthor <michael.the.ranidae@gmail.com>
 * @method mixed runAction()
 */
abstract class Action {
    /**
     * 当前动作所在分组名称
     * @var string
     */
    private $groupName = null;
    
    /**
     *获取当前动作所在分组名称
     * @return string
     */
    public function getGroupName() {
        return $this->groupName;
    }
    
    /**
     * 当前动作名称
     * @var string
     */
    private $name = null;
    
    /**
     * 获取当前动作名称
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * 初始化动作
     * @param string $groupName 分组名称
     * @param string $name 动作名称
     */
    public function __construct( $groupName, $name) {
        $this->groupName = $groupName;
        $this->name = $name;
        $this->init();
    }
    
    /**
     * 初始化操作, 具体初始化在子类实现。
     * @return void
     */
    protected function init() {}
        
    /**
     * 当前动作的参数列表
     * @var array
     */
    private $parameters = array();
    
    /**
     * 根据名称获取动作参数
     * @param string $name 参数名称
     * @param mixed $default 默认值
     * @return mixed
     */
    public function getParameter( $name, $default=null ) {
        return key_exists($name, $this->parameters) ? $this->parameters[$name] : $default;
    }
    
    /**
     * 执行动作
     * 如果"beforeRunAction()"返回false， 则停止运行，并返回false。
     * @param array $parameters 执行参数
     * @return boolean
     */
    public function run( $parameters=array() ){
        $this->parameters = $parameters;
        if ( false === $this->beforeRunAction() ) {
            return false;
        }
        $result = $this->doRunAction($parameters);
        $this->afterRunAction();
        return $result;
    }
    
    /**
     * 将参数应用到动作处理方法然后执行
     * @param array $parameters
     * @return boolean|mixed
     */
    protected function doRunAction($parameters) {
        $handlerName = 'runAction';
        if ( !method_exists($this, $handlerName) || !is_callable(array($this, $handlerName)) ) {
            throw new \Exception("Can not find action handler \"runAction()\".");
        }
        
        $paramsToMethod = array();
        $class = new \ReflectionClass($this);
        $method = $class->getMethod($handlerName);
        
        $parameterInfos = $method->getParameters();
        foreach ( $parameterInfos as $parmInfo ) {
            /* @var $parmInfo \ReflectionParameter */
            $name = $parmInfo->getName();
            if ( isset($parameters[$name]) ) {
                $paramsToMethod[$name] = $parameters[$name];
            } else if ( $parmInfo->isOptional() && $parmInfo->isDefaultValueAvailable() ) {
                $paramsToMethod[$name] = $parmInfo->getDefaultValue();
            } else {
                throw new \Exception('Parameters to action handler is not available.');
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
}