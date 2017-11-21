<?php
namespace X\Core\Environment;
use X\Core\Component\Exception;
/**
 * 运行环境处理器
 * @author Michael Luthor <michaelluthor@163.com>
 * @method void init() 初始化运行环境
 * @method string getName() 获取运行环境名称
 * @method array getParameters() 获取运行环境参数
 */
class Environment {
    /**
     * 运行环境实例
     * @var X\Core\Environment\Util\Handler
     */
    private $handler = null;
    
    /**
     * 初始化管理器
     * @throws Exception 找不到对应的环境处理方式
     */
    public function __construct() {
        $handlerName = php_sapi_name();
        $handlerName = str_replace('-', '_', $handlerName);
        $handlerClass = '\\X\\Core\\Environment\\Handler\\'.ucfirst($handlerName);
        if ( !class_exists($handlerClass) ) {
            throw new Exception("Unable to find a environment handler '$handlerClass'.");
        }
        $this->handler = new $handlerClass();
    }
    
    /**
     * 调用运行环境实例的方法。
     * @param string $name
     * @param array $params
     */
    public function __call( $name, $params ) {
        if ( !method_exists($this->handler, $name) ) {
            throw new Exception("Method '$name' does not exists in '".get_class($this->handler)."'.");
        }
        return call_user_func_array(array($this->handler, $name), $params);
    }
}