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
 * Truncate action builder
 * 
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Truncate extends ActionAboutTable {
    /**
     * Add action to query.
     * @return Truncate
     */
    protected function buildHandlerName() {
        if ( null === $this->name ) {
            throw new Exception("Name can not be empty to truncate the table.");
        }
        $this->sqlCommand[] = 'TRUNCATE TABLE '.$this->quoteTableName($this->name);
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Action\Base::getBuildHandlers() Base::getBuildHandlers()
     */
    protected function getBuildHandlers() {
        return array('name');
    }
}