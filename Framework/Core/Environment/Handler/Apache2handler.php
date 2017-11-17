<?php
namespace X\Core\Environment\Handler;

/**
 * 
 */
use X\Core\Environment\Util\Handler;

/**
 *
 */
class Apache2handler extends Handler {
    /**
     * (non-PHPdoc)
     * @see \X\Core\Environment\Util\Handler::getName()
     */
    public function getName() {
        return 'apache2handler';
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Environment\Util\Handler::initParameters()
     */
    protected function initParameters() {
        $this->parameters = array_merge($_GET, $_POST, $_REQUEST);
    }
}