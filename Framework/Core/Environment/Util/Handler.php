<?php
namespace X\Core\Environment\Util;

/**
 * 
 */
abstract class Handler {
    /**
     * @var array
     */
    protected $parameters = array();
    
    /**
     * 
     */
    public function __construct() {
        $this->initParameters();
    }
    
    /**
     *
     */
    abstract protected function initParameters();
    
    /**
     *
     */
    abstract public function getName();
    
    /**
     *
     */
    public function getParameters() {
        return $this->parameters;
    }
    
    /**
     * @return void
     */
    public function init() {}
}