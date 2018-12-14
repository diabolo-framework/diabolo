<?php
namespace X\Service\Action;
use X\Service\Action\Handler\ActionBase;
use X\Core\Component\Stringx;
class ActionGroup {
    /** @var string */
    private $name = null;
    /** @var string */
    private $namespace = null;
    /** @var ActionBase */
    private $runningAction = null;
    /** @var string[] */
    private $registeredActions = array();
    /** @var string */
    private $defaultAction = null;
    /** @var string */
    private $viewPath = null;
    
    /**
     * @param string $name
     * @return void
     */
    public function __construct( $name, $namespace=null ) {
        $this->name = $name;
        $this->namespace = rtrim($namespace, '\\');
    }
    
    /**
     * @return \X\Service\Action\Handler\ActionBase
     */
    public function getRunningAction() {
        return $this->runningAction;
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function runAction( $name=null, $params=array() ) {
        if ( empty($name) ) {
            $name = $this->defaultAction;
        }
        if ( isset($this->registeredActions[$name]) ) {
            $actionClassName = $this->registeredActions[$name];
        } else {
            $name = implode('\\', array_map('ucfirst', explode('/', $name)));
            $actionClassName = $this->namespace.'\\'.Stringx::middleSnakeToCamel($name);
        }
        
        if ( !class_exists($actionClassName) ) {
            throw new ActionException("can not fine action handler `{$name}`");
        }
        
        /** @var $action ActionBase */
        $action = new $actionClassName($this, $params);
        return $action->execute();
    }
    
    /**
     * @param unknown $name
     * @param unknown $handler
     * @return self
     */
    public function registeAction( $name, $handler ) {
        if ( !is_subclass_of($handler, ActionBase::class) ) {
            throw new ActionException('action handler must instance of '.ActionBase::class);
        }
        $this->registeredActions[$name] = $handler;
        return $this;
    }
    
    /**
     * @param unknown $name
     * @return \X\Service\Action\ActionGroup
     */
    public function setDefaultAction( $name ) {
        $this->defaultAction = $name;
        return $this;
    }
    
    /**
     * @param unknown $path
     * @return self
     */
    public function setViewPath( $path ) {
        $this->viewPath = rtrim($path, '/\\');
        return $this;
    }
    
    /** @return string */
    public function getViewPath() {
        return $this->viewPath;
    }
    
    /** @return string */
    public function getName() {
        return $this->name;
    }
}