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
 * AlterTable action builder
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class AlterTable extends ActionAboutTable {
    /**
     * Set AlterTable action to add column.
     * @param string $name
     * @param \X\Service\XDatabase\Core\Table\Column $definition
     * @return AlterTable
     */
    public function addColumn( $name, $definition ) {
        $this->action['handler'] = 'addColumn';
        $this->action['parms'] = array('name'=>$name, 'definition'=>$definition);
        return $this;
    }
    
    /**
     * Set AlterTable action to "add index"
     * @param string $name The name of the index.
     * @param array $columns The column list that index contains.
     * @return AlterTable
     */
    public function addIndex( $name, $columns ) {
        if ( empty($columns) ) {
            throw new Exception('Unable to add index without columns.');
        }
        $this->action['handler'] = 'addIndex';
        $this->action['parms'] = array('name'=>$name, 'columns'=>$columns);
        return $this;
    }
    
    /**
     * Set AlterTable action to "add primary key"
     * @param array|string $columns The column names to be primary key
     * @return AlterTable
     */
    public function addPrimaryKey( $columns ) {
        if ( empty($columns) ) {
            throw new Exception('Unable to add primary key without columns.');
        }
        $this->action['handler'] = 'addPrimaryKey';
        $this->action['parms'] = array('columns' => $columns);
        return $this;
    }
    
    /**
     * Set AlterTable action to "add unique"
     * @param array|string $columns The columns that unique contains.
     * @return AlterTable
     */
    public function addUnique( $columns ) {
        if ( empty($columns) ) {
            throw new Exception('Unable to add unique without columns.');
        }
        $this->action['handler'] = 'addUnique';
        $this->action['parms'] = array('columns' => $columns);
        return $this;
    }
    
    /**
     * Set AlterTable action to "change column"
     * @param string $column The name of column to change
     * @param string $newName The new name of the column
     * @param string $definition The definition of the column
     * @return AlterTable
     */
    public function changeColumn( $column, $newName, $definition) {
        $this->action['handler'] = 'changeColumn';
        $this->action['parms'] = array('column'=>$column, 'newName'=>$newName, 'definition'=>$definition);
        return $this;
    }
    
    /**
     * Set AlterTable action to "drop column"
     * @param string $name The name of column to drop
     * @return AlterTable
     */
    public function dropColumn( $name ) {
        $this->action['handler'] = 'dropColumn';
        $this->action['parms']['name'] = $name;
        return $this;
    }
    
    /**
     * Set AlterTable action to "drop primary key"
     * @return AlterTable
     */
    public function dropPrimaryKey() {
        $this->action['handler'] = 'dropPrimaryKey';
        return $this;
    }
    
    /**
     * Set AlterTable action to "drop index"
     * @param string $name The name of index to drop
     * @return AlterTable
     */
    public function dropIndex( $name ) {
        $this->action['handler'] = 'dropIndex';
        $this->action['parms']['name'] = $name;
        return $this;
    }
    
    /**
     * Get AlterTable name part of command for query.
     * This method is called by toString() method.
     * @return string
     */
    protected function buildHandlerName() {
        if ( null === $this->name ) {
            throw new Exception('Name can not be empty to alter the table.');
        }
        $this->sqlCommand[] = 'ALTER TABLE '.$this->quoteTableName($this->name);
        return $this;
    }
    
    /**
     * Get action part of query command.
     * @return void
     */
    protected function buildHandlerAction() {
        $handler = sprintf('actionHandler%s', ucfirst($this->action['handler']));
        $this->$handler();
    }
    
    /**
     * Get the name list of handlers to build the query string
     * @see \X\Database\SQL\Action\Base::getBuildHandlers()
     * @return array
     */
    protected function getBuildHandlers() {
        return array('name','action');
    }
    
    /**
     * This value contains the toString handler and parms to that handler.
     * @var array
     */
    protected $action = array('handler'=>null, 'parms'=>null);
    
    /**
     * Get the action handler string of add column to query command.
     * @return void
     */
    protected function actionHandlerAddColumn() {
        $name = $this->quoteColumnName($this->action['parms']['name']);
        $definition = $this->action['parms']['definition'];
        $this->sqlCommand[] = 'ADD '.$name.' '.$definition;
    }
    
    /**
     * Add the action handler string of add index to query command.
     * @return void
     */
    protected function actionHandlerAddIndex() {
        $name = $this->quoteColumnName($this->action['parms']['name']);
        $columns = implode(',', $this->quoteColumnNames($this->action['parms']['columns']));
        $this->sqlCommand[] = 'ADD INDEX '.$name.' ('.$columns.')';
    }
    
    /**
     * Add the action handler string of add primary key to query command.
     * @return void
     */
    protected function actionHandlerAddPrimaryKey() {
        $columns = $this->action['parms']['columns'];
        $columns = is_array($columns) ? $columns : array($columns);
        $columns = $this->quoteColumnNames($columns);
        $columns = implode(',', $columns);
        $this->sqlCommand[] = 'ADD PRIMARY KEY ('.$columns.')';
    }
    
    /**
     * Add the action handler string of "add unique" to query command.
     * @return void
     */
    protected function actionHandlerAddUnique() {
        $columns = $this->action['parms']['columns'];
        $columns = is_array($columns) ? $columns : array($columns);
        $columns = $this->quoteColumnNames($columns);
        $columns = implode(',', $columns);
        $this->sqlCommand[] = 'ADD UNIQUE ( '.$columns.' )';
    }
    
    /**
     * Add the action handler string of "change column" to query command.
     * @return void
     */
    protected function actionHandlerChangeColumn() {
        $query  = 'CHANGE COLUMN ';
        $query .= $this->quoteColumnName($this->action['parms']['column']).' ';
        $query .= $this->quoteColumnName($this->action['parms']['newName']).' ';
        $query .= $this->action['parms']['definition'];
        $this->sqlCommand[] = $query;
    }
    
    /**
     * Add the action handler string of "drop column" to query command.
     * @return void
     */
    protected function actionHandlerDropColumn() {
        $name = $this->quoteColumnName($this->action['parms']['name']);
        $this->sqlCommand[] = 'DROP COLUMN '.$name;
    }
    
    /**
     * Add the action handler string of "drop column" to query command.
     * @return void
     */
    protected function actionHandlerDropPrimaryKey() {
        $this->sqlCommand[] = 'DROP PRIMARY KEY';
    }
    
    /**
     * Add the action handler string of "drop index" to query command.
     * @return void
     */
    protected function actionHandlerDropIndex() {
        $name = $this->quoteColumnName($this->action['parms']['name']);
        $this->sqlCommand[] = 'DROP INDEX '.$name;
    }
}