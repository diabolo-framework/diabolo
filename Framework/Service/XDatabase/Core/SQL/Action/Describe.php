<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Action;
/**
 * 
 */
use X\Service\XDatabase\Core\SQL\Util\ActionAboutTable;
use X\Service\XDatabase\Core\Util\Exception;
/**
 * Update
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @version 0.0.0
 * @since   0.0.0
 */
class Describe extends ActionAboutTable {
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Action\Base::getBuildHandlers() Base::getBuildHandlers()
     */
    protected function getBuildHandlers() {
        return array('action','table');
    }
    
    /**
     * Add action name into query
     * @return Update
     */
    protected function buildHandlerAction() {
        $this->sqlCommand[] = 'DESCRIBE';
        return $this;
    }
    
    /**
     * Add table string into query.
     * @return Update
     */
    protected function buildHandlerTable() {
        if ( null === $this->name ) {
            throw new Exception('Unable to do describe if table name is empty.');
        }
        $this->sqlCommand[] = $this->quoteTableName($this->name);
        return $this;
    }
}