<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Action;

/**
 * Use statements
 */
use X\Service\XDatabase\Core\Util\Exception;
use X\Service\XDatabase\Core\SQL\Util\ActionAboutTable;

/**
 * CreateTable
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class CreateTable extends ActionAboutTable {
    /**
     * Set the column definitions for the table.
     * @param array $columns The definetions of each column.
     * @return CreateTable
     */
    public function columns( $columns ) {
        if ( empty($columns) ) {
            throw new Exception('No columns information for creating the table.');
        }
        $this->columns = $columns;
        return $this;
    }
    
    /**
     * set primary key to create action.
     * @param string $name
     * @return \X\Service\XDatabase\Core\SQL\Action\CreateTable
     */
    public function primaryKey( $name ) {
        $this->primaryKey = $name;
        return $this;
    }
    
    /**
     * Get the handlers array, each element is the method name
     * of the subclass class
     * @see \X\Database\SQL\Action\Base::getBuildHandlers()
     * @return array
     */
    protected function getBuildHandlers() {
        return array('name','(', 'column','primaryKey',')');
    }
    
    /**
     * Add name part of query string.
     * @return string
     */
    protected function buildHandlerName() {
        if ( null === $this->name ) {
            throw new  Exception('Name can not be empty to create the table.');
        }
        $this->sqlCommand[] = 'CREATE TABLE '.$this->quoteTableName($this->name);
        return $this;
    }
    
    /**
     * Definitions of new table columns.
     * @var array
     */
    protected $columns = null;
    
    /**
     * Add the column definination part of query.
     * @return void
     */
    protected function buildHandlerColumn() {
        $columns = array();
        foreach ( $this->columns as $name => $definition ) {
            $columns[] = $this->quoteColumnName($name).' '.$definition;
        }
        $this->sqlCommand[] = implode(',', $columns);
    }
    
    /**
     * this value hold the name of primary key
     * @var string
     */
    protected $primaryKey = null;
    
    /**
     * create sql partile command for primary key.
     */
    protected function buildHandlerPrimaryKey() {
        if ( null === $this->primaryKey ) {
            return;
        }
        $this->sqlCommand[] = ', PRIMARY KEY ('.$this->quoteColumnName($this->primaryKey).')';
    }
}