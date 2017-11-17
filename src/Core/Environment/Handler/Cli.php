<?php
namespace X\Core\Environment\Handler;

/**
 * 
 */
use X\Core\Environment\Util\Handler;

/**
 * 
 */
class Cli extends Handler {
    /**
     * (non-PHPdoc)
     * @see \X\Core\Environment\Util\Handler::getName()
     */
    public function getName() {
        return 'cli';
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Environment\Util\Handler::initParameters()
     */
    protected function initParameters() {
        global $argv;
        $parameters = array();
        foreach ( $argv as $index => $parm ) {
            if ( '--' !== substr($parm, 0, 2) ) {
                continue;
            }
            $parm = explode('=', $parm);
            $name = $parm[0];
            $value = isset($parm[1]) ? trim($parm[1]) : true;
            $name = substr($name, 2);
            $parameters[trim($name)] = $value;
        }
        $this->parameters = $parameters;
    }
}