<?php
namespace X\Service\XAction;
use X\Core\X;
use X\Core\Component\ConfigurationArray;
/**
 * XAction 服务用于执行请求动作并输出结果
 * @author  Michael Luthor <michaelluthor@163.com>
 * @package \X\Service\XAction
 */
class Service extends \X\Core\Service\XService {
    /**
     * 服务名称
     * @var string
     */
    protected static $serviceName = 'XAction';
    
    /**
     * 服务被加载后初始化服务
     * @see \X\Core\Service\XService::onLoaded()
     * @return void
     */
    protected function onLoaded() {
        parent::onLoaded();
        
        $error500Handler = $this->getConfiguration()->get('Error500Handler');
        if ( null !== $error500Handler ) {
            set_error_handler(function($errorLevel, $errorMessage, $file, $line) {
                $this->triggerErrorHandler('500', func_get_args());
                return true;
            }, intval(ini_get('error_reporting')));
            set_exception_handler(function ( \Exception $ex ) {
                $this->triggerErrorHandler('500', func_get_args());
                return true;
            });
        }
    }
    
    /**
     * 启动服务
     * @see \X\Core\Service\XService::start()
     * @return void
     */
    public function start() {
        parent::start();
        $this->parameterManager = new ConfigurationArray();
    }
    
    /**
     * 停止服务
     * @see \X\Core\Service\XService::stop()
     * @return void
     */
    public function stop() {
        $this->parameterManager = null;
        parent::stop();
    }
    
    /**
     * 用于动作执行的参数管理器
     * @var ConfigurationArray
     */
    private $parameterManager = null;
    
    /**
     * 获取服务参数管理器, 用于增加/删除参数。设置参数也是由该管理器操作， 
     * 
     * 例如：$this->getParameterManager()->set($name, $value);
     * @return ConfigurationArray
     */
    public function getParameterManager() {
        return $this->parameterManager;
    }
    
    /**
     * 动作分组信息
     * @var array
     */
    private $groups = array(
      /*'name' => array(
            'namespace' => '\\X\\Module\\Demo\\Action', # 分组命名空间
            'running' => null,               # 当前分组正在运行的动作
            'registered_actions' => array(), # 注册到该分组的动作
            'options' => array(              # 分组配置
                'default' => 'index',        # 分组默认动作
                'viewPath' => null,          # 分组视图路径 
            ),
        ),*/
    );
    
    /**
     * 增加动作分组到服务
     * @param string $name 分组名称
     * @param string $namespace 分组动作的基础命名空间，在获取动作时，将使用该
     *      命名空间去查找动作，例如命名空间为"\X\Module\Demo\Action", 动作为
     *      "test/example", 则最后的action类为"\X\Module\Demo\Action\Test\Example"
     * @throws \Exception 分组已经存在
     * @return void
     */
    public function addGroup( $name, $namespace=null ) {
        if ( $this->hasGroup($name) ) {
            throw new \Exception('Action group "'.$name.'" already exists.');
        }
        
        $group = array();
        $group['namespace']             = (null===$namespace) ? null : rtrim($namespace, '\\');
        $group['running']               = null;
        $group['registeredActions']     = array();
        $group['options']               = array(
            'defaultAction'             => null,
            'viewPath'                  => null,
        );
        $this->groups[$name]            = $group;
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
        
        $actionParamName = $this->getConfiguration()->get('ActionParamName', 'action');
        $actionName = $this->getGroupOption($name, 'defaultAction');
        $actionName = $this->getParameterManager()->get($actionParamName, $actionName);
        if ( empty($actionName) ) {
            throw new \Exception('Can not find available action in group "'.$name.'".');
        }
    
        return $this->runAction($name, $actionName);
    }
    
    /**
     * 当前正在运行的动作实例
     * @var \X\Service\XAction\Util\Action
     */
    private $runningAction = null;
    
    /**
     * 获取当前正在运行的动作
     * @return  \X\Service\XAction\Util\Action
     */
    public function getRunningAction() {
        return $this->runningAction;
    }
    
    /**
     * 运行指定分组下的动作
     * @param string $group 分组名
     * @param string $action 动作名称
     * @return mixed
     */
    public function runAction($group, $action) {
        $action = $this->getActionByName($group, $action);
        $this->groups[$group]['running'] = $action;
        $parameters = $this->getParameterManager()->toArray();
        
        $actionParamName = $this->getConfiguration()->get('ActionParamName', 'action');
        unset($parameters[$actionParamName]);
        $this->runningAction = $action;
        $result = $action->run($parameters);
        $this->runningAction = null;
        return $result;
    }
    
    /**
     * 设置分组选项
     * @param string $groupName 分组名
     * @param string $name 配置名称
     * <li>defaultAction 默认动作名称</li> 
     * <li>viewPath 视图路径</li>
     * @param mixed $value 配置值
     * @throws \Exception 分组不存在
     * @return void
     */
    public function setGroupOption( $groupName, $name, $value ){
        if ( !$this->hasGroup($groupName) ) {
            throw new \Exception('Action group "'.$groupName.'" does not exists.');
        }
        $this->groups[$groupName]['options'][$name] = $value;
    }
    
    /**
     * 获取分组选项
     * @param string $groupName 分组名
     * @param string $name 配置名称
     * <li>defaultAction 默认动作名称</li> 
     * <li>viewPath 视图路径</li>
     * @param string $default 默认值
     * @throws \Exception 分组不存在
     * @return void
     */
    public function getGroupOption( $groupName, $name, $default=null ) {
        if ( !$this->hasGroup($groupName) ) {
            throw new \Exception('Action group "'.$groupName.'" does not exists.');
        }
        if ( array_key_exists($name, $this->groups[$groupName]['options']) ) {
            return $this->groups[$groupName]['options'][$name];
        } else {
            return $default;
        }
    }
    
    /**
     * 注册动作到分组
     * @param string $group 分组名
     * @param string $action 动作名
     * @param callable $handler 动作处理器
     * @return void
     */
    public function register( $group, $action, $handler ) {
        if ( !$this->hasGroup($group) ) {
            throw new \Exception('Action group "'.$group.'" does not exists.');
        }
        $this->groups[$group]['registeredActions'][$action] = $handler;
    }
    
    /**
     * 通过动作名称获取分组下的动作实例
     * @param string $group 分组名
     * @param string $action 动作类名
     * @throws Exception 动作不存在
     * @return \X\Service\XAction\Util\Action
     */
    public function getActionByName( $group, $action ) {
        if ( isset($this->groups[$group]['registeredActions'][$action]) ) {
            $handler = $this->groups[$group]['registeredActions'][$action];
            if ( class_exists($handler) ) {
                $handler = new $handler($group, $action);
                return $handler;
            }
        }
        
        $actionClass = array_map('ucfirst', explode('-', $action));
        $actionClass = implode('', $actionClass);
        
        $actionClass = array_map('ucfirst', explode('/', $actionClass));
        $actionClass = implode('\\', $actionClass);
        $namespace = $this->groups[$group]['namespace'];
        $actionClass = $namespace.'\\'.$actionClass;
        if ( class_exists($actionClass, true) ) {
            $action = new $actionClass($group, $action);
            return $action;
        }
        
        throw new \Exception('Can not find Action "'.$action.'" in group "'.$group.'".');
    }
    
    /**
     * 触发动作错误， 比如404,或者500错误。
     * @param string $errorName 错误名称
     * @param array $params 错误参数
     * @return void
     */
    public function triggerErrorHandler( $errorName, $params=array() ) {
        $handler = $this->getConfiguration()->get('Error'.$errorName.'Handler');
        
        ob_start();
        ob_end_clean();
        
        switch ( $errorName ) {
        case '404' : 
            if ( null === $handler ) {
                $handler = '404 NOT FOUND!';
            }
            header('HTTP/1.0 404 NOT FOUND'); 
            break;
        default : break;
        }
        
        if ( is_callable($handler) ) {
            call_user_func_array($handler, array());
        } else {
            echo $handler;
        }
        X::system()->stop();
    }
}