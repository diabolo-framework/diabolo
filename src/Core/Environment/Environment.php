<?php
namespace X\Core\Environment;

/**
 * 
 */
use X\Core\Util\Exception;

/**
 * @method void init()
 * @method string getName()
 * @method array getParameters()
 */
class Environment {
    /**
     * @var X\Core\Environment\Util\Handler
     */
    private $handler = null;
    
    /**
     * @throws Exception
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