<?php
namespace X\Core\Environment\Handler;

/**
 * 
 */
use X\Core\Environment\Util\Handler;

/**
 *
 */
class Apache extends Handler {
    /**
     * (non-PHPdoc)
     * @see \X\Core\Environment\Util\Handler::getName()
     */
    public function getName() {
        return 'apache';
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Environment\Util\Handler::initParameters()
     */
    protected function initParameters() {
        $this->parameters = array_merge($_GET, $_POST, $_REQUEST);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Environment\Util\Handler::init()
     */
    public function init() {
        parent::init();
    }
}