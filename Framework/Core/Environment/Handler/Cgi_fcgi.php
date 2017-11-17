<?php
namespace X\Core\Environment\Handler;

/**
 * 
 */
use X\Core\Environment\Util\Handler;

/**
 *
 */
class Cgi_fcgi extends Handler {
    /**
     * (non-PHPdoc)
     * @see \X\Core\Environment\Util\Handler::getName()
     */
    public function getName() {
        return 'cgi-fcgi';
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Environment\Util\Handler::initParameters()
     */
    protected function initParameters() {
        $this->parameters = array_merge($_GET, $_POST, $_REQUEST);
    }
}