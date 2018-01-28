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
 * Rename action builder
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Rename extends ActionAboutTable {
    /**
     * The new name of the table.
     * @var string
     */
    protected $newName = null;
    
    /**
     * Set new name for table
     * @param string $name The new name of the table.
     * @return Rename
     */
    public function newName( $name ) {
        $this->newName = $name;
        return $this;
    }
    
    /**
     * Add command into query.
     * @return Rename
     */
    protected function buildHandlerName() {
        if ( null === $this->name ) {
            throw new Exception('Name can not be empty to rename the table.');
        }
        if ( null === $this->newName ) {
            throw new Exception('New name can not be empty to the table.');
        }
        if ( $this->name === $this->newName ) {
            throw new Exception('The old name and new name could not be same.');
        }
        
        $oldName = $this->quoteColumnName($this->name);
        $newName = $this->quoteColumnName($this->newName);
        $this->sqlCommand[] = 'RENAME TABLE '.$oldName.' TO '.$newName;
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