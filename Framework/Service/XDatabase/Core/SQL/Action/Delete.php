<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Action;

/**
 * Use statements
 */
use X\Service\XDatabase\Core\Util\Exception as DBException;
use X\Service\XDatabase\Core\SQL\Util\ActionWithCondition;

/**
 * Delete
 * @author  Michael Luthor <michael.the.ranidae@gamil.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Delete extends ActionWithCondition {
    /**
     * Set the table name that delete from.
     * @param string $table The table name that delte from
     * @return Delete
     */
    public function from( $table ) {
        $this->tables[] = $table;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Action\Base::getBuildHandlers()  parent::getBuildHandlers()
     */
    protected function getBuildHandlers() {
        return array('action','tableName','condition','limit');
    }
    
    /**
     * Add action's name into query.
     * @return Delete
     */
    protected function buildHandlerAction() {
        $this->sqlCommand[] = 'DELETE';
        return $this;
    }
    
    /**
     * The table names that will delete from.
     * @var string[]
     */
    protected $tables = array();
    
    /**
     * Build from string, this method is called by toString
     * @return string
     */
    protected function buildHandlerTableName() {
        if ( 0 == count($this->tables) ) {
            throw new DBException('Delete action requires at least one table to delete from.');
        }
        $tables = array_map(array($this, 'quoteTableName'), $this->tables);
        $this->sqlCommand[] = 'FROM '.implode(',', $tables);
        return $this;
    }
}