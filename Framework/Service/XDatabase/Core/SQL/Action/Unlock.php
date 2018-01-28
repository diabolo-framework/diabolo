<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Action;

/**
 * 
 */
use X\Service\XDatabase\Core\SQL\Util\ActionBase;

/**
 * lock action builder
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Unlock extends ActionBase {
    /**
     * Add command into query.
     * @return Rename
     */
    protected function buildHandlerUnlock() {
        $this->sqlCommand[] = 'UNLOCK TABLES';
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Action\Base::getBuildHandlers() Base::getBuildHandlers()
     */
    protected function getBuildHandlers() {
        return array('unlock');
    }
}