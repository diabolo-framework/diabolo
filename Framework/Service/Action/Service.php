<?php
namespace X\Service\Action;
use X\Core\X;
use X\Core\Service\XService;
/**
 * Action 服务用于执行请求动作并输出结果
 */
class Service extends XService {
    /** @var string config */
    protected $actionParamName = 'action';
    /** @var string */
    protected $globalViewPath = 'View';
    
    /** @var array */
    private $parameters = array();
    /** @var ActionGroup[] */
    private $groups = array();
    /** @var string */
    private $runningGroup = null;
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setParam( $name, $value ) {
        $this->parameters[ $name ] = $value;
        return $this;
    }
    
    /**
     * @param array $values
     * @return self
     */
    public function setParams( array $values ) {
        $this->parameters = array_merge($this->parameters, $values);
        return $this;
    }
    
    /**
     * 增加动作分组到服务
     * @param string $name 分组名称
     * @param string $namespace 分组动作的基础命名空间
     * @throws \Exception 分组已经存在
     * @return ActionGroup
     */
    public function addGroup( $name, $namespace=null ) {
        if ( $this->hasGroup($name) ) {
            throw new \Exception('Action group "'.$name.'" already exists.');
        }
        
        $group = new ActionGroup($name, $namespace);
        $this->groups[$name] = $group;
        return $group;
    }
    
    /**
     * 检查给定的组名是否存在
     * @param string $name 组名
     * @return boolean
     */
    public function hasGroup( $name ) {
        return isset($this->groups[$name]);
    }
    
    /**
     * 通过给定的参数运营分组,并返回执行结果
     * @param string $name 组名
     * @throws \Exception 分组不存在
     * @throws \Exception 找不到可执行的动作
     * @return mixed
     */
    public function runGroup($name){
        if ( !$this->hasGroup($name) ) {
            throw new \Exception('Action group "'.$name.'" does not exists.');
        }
        
        $actionName = null;
        if ( isset($this->parameters[$this->actionParamName]) ) {
            $actionName = $this->parameters[$this->actionParamName];
        }
        
        return $this->runAction($name, $actionName);
    }
    
    /**
     * 获取当前正在运行的动作
     * @return  \X\Service\XAction\Util\Action
     */
    public function getRunningAction() {
        if ( null === $this->runningGroup ) {
            return null;
        }
        return $this->groups[$this->runningGroup]->getRunningAction();
    }
    
    /**
     * 运行指定分组下的动作
     * @param string $group 分组名
     * @param string $action 动作名称
     * @return mixed
     */
    public function runAction($groupName, $actionName) {
        $group = $this->groups[$groupName];
        $this->runningGroup = $groupName;
        $result = $group->runAction($actionName, $this->parameters);
        $this->runningGroup = null;
        return $result;
    }
    
    /**
     * @param unknown $name
     * @return string
     */
    public function getGlobalViewPathByName( $name, $type ) {
        $path = $this->globalViewPath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$name.'.php';
        return X::system()->getPath($path);
    }
}