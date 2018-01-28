<?php
namespace X\Module\Oauth2\Util;
abstract class ActionBase {
    /** @var array */
    private $parameters = array();
    
    /** @param $params array */
    public function __construct( $params ) {
        $this->parameters = $params;
    }
    
    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default=null) {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }
    
    /** @return void */
    public function exec() {
        $this->handle();
    }
    
    /** @return void */
    abstract protected  function handle();
}