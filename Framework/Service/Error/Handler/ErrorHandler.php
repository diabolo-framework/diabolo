<?php
namespace X\Service\Error\Handler;
use X\Service\Error\RuntimeError;
/**
 * error processor base class.
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class ErrorHandler {
    /** @var RuntimeError */
    private $error = null;
    
    /**
     * @param unknown $error
     * @param unknown $config
     */
    public function __construct( $error, $config ) {
        $this->error = $error;
        foreach ( $config as $key => $value ) {
            if ( property_exists($this, $key) ) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * @return \X\Service\Error\RuntimeError
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * process error.
     * @return void
     */
    abstract public function process();
}