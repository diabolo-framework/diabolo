<?php
namespace X\Core\Shortcut;

/**
 *
 */
use X\Core\Component\Manager as UtilManager;
use X\Core\Component\Exception;

/**
 * 
 */
class Manager extends UtilManager {
    /**
     * @var array
     */
    private $shortcuts = array();
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Component\Manager::start()
     */
    public function start() {
        parent::start();
        $this->shortcuts = array();
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Component\Manager::stop()
     */
    public function stop() {
        $this->shortcuts = array();
        parent::stop();
    }
    
    /**
     * @param string $name
     * @param callable $handler
     * @throws Exception
     */
    public function register( $name, $handler ) {
        if ( $this->has($name) ) {
            throw new Exception('Shortcut function "'.$name.'" already exists.');
        }
        if ( !is_callable($handler) ) {
            throw new Exception('The handler of shortcut function "'.$name.'" is not callable.');
        }
        $this->shortcuts[$name] = $handler;
    }
    
    /**
     * @param string $name
     */
    public function unregister($name) {
        if ( !$this->has($name) ) {
            throw new Exception('Shortcut function "'.$name.'" does not exists.');
        }
        unset($this->shortcuts[$name]);
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function has($name){
        return isset($this->shortcuts[$name]);
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function call($name, $params){
        if ( !$this->has($name) ) {
            throw new Exception('Shortcut function "'.$name.'" does not exists.');
        }
        return call_user_func_array($this->shortcuts[$name], $params);
    }
}