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
 * DropTable action builder
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class DropTable extends ActionAboutTable {
    /**
     * Add action name into query.
     * @return DropTable
     */
    protected function buildHandlerName() {
        if ( null === $this->name ) {
            throw new Exception('Name can not be empty to delete the table.');
        }
        $this->sqlCommand[] = 'DROP TABLE '.$this->quoteColumnName($this->name);
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Action\Base::getBuildHandlers() parent::getBuildHandlers()
     */
    protected function getBuildHandlers() {
        return array('name');
    }
}