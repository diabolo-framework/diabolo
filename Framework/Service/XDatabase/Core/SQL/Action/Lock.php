<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Action;

/**
 * 
 */
use X\Service\XDatabase\Core\Util\Exception;
use X\Service\XDatabase\Core\SQL\Util\ActionAboutTable;

/**
 * lock action builder
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Lock extends ActionAboutTable {
    /**
     * @var string
     */
    protected $type = null;
    
    /**
     * @param unknown $type
     * @return \X\Service\XDatabase\Core\SQL\Action\Lock
     */
    public function setType( $type ) {
        $this->type = $type;
        return $this;
    }
    
    /**
     * Add command into query.
     * @return Rename
     */
    protected function buildHandlerLock() {
        if ( null === $this->type ) {
            throw new Exception('type can not be empty to lock the table.');
        }
        
        $name = $this->quoteColumnName($this->name);
        $this->sqlCommand[] = 'LOCK TABLE '.$name.' '.$this->type;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Action\Base::getBuildHandlers() Base::getBuildHandlers()
     */
    protected function getBuildHandlers() {
        return array('lock');
    }
}