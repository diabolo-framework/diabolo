<?php
namespace X\Service\Database\Migration;
use X\Service\Database\Database;
use X\Service\Database\DatabaseException;
use X\Service\Database\Table;
use X\Service\Database\Query;
abstract class Migration {
    /** @var callable */
    private $processHandler = null;
    /** @return string|Database */
    abstract protected function getDb();
    /** */
    abstract public function up();
    /** */
    abstract public function down();
    
    /**
     * @param callable $handler
     * @throws DatabaseException
     * @return self
     */
    public function setProcessHandler( $handler ) {
        if ( !is_callable($handler) ) {
            throw new DatabaseException('migration process handler is not callable');
        }
        $this->processHandler = $handler;
        return $this;
    }
    
    /**
     * @param unknown $name
     * @param unknown $process
     */
    private function processHandler( $name, $process ) {
        if ( is_callable($this->processHandler) ) {
            call_user_func_array($this->processHandler, array($name, $process));
        }
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $columns
     */
    protected function createTable($tableName, $columns) {
        Table::create($this->getDb(), $tableName, $columns);
        $this->processHandler('CreateTable', array(
            'tableName' => $tableName,
        ));
    }
    
    /**
     * @param unknown $tableName
     */
    protected function dropTable($tableName) {
        Table::get($this->getDb(), $tableName)->drop();
        $this->processHandler('DropTable',array(
            'tableName' => $tableName,
        ));
    }
    
    /**
     * @param unknown $tableName
     */
    protected function truncateTable( $tableName ) {
        Table::get($this->getDb(), $tableName)->truncate();
        $this->processHandler('TruncateTable',array(
            'tableName' => $tableName,
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $newName
     */
    protected function renameTable($tableName, $newName) {
        Table::get($this->getDb(), $tableName)->rename($newName);
        $this->processHandler('RenameTable',array(
            'tableName' => $tableName,
            'newName' => $newName,
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $colName
     * @param unknown $defination
     */
    protected function addColumn($tableName, $colName, $defination){
        Table::get($this->getDb(), $tableName)->addColumn($colName, $defination);
        $this->processHandler('AddColumn',array(
            'tableName' => $tableName,
            'colName' => $colName,
            'defination' => $defination,
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $colName
     */
    protected function dropColumn( $tableName, $colName ){
        Table::get($this->getDb(), $tableName)->dropColumn($colName);
        $this->processHandler('DropColumn',array(
            'tableName' => $tableName,
            'colName' => $colName,
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $colName
     * @param unknown $newName
     */
    protected function renameColumn( $tableName, $colName, $newName ) {
        Table::get($this->getDb(), $tableName)->dropColumn($colName);
        $this->processHandler('RenameColumn',array(
            'tableName' => $tableName,
            'colName' => $colName,
            'newName' => $newName
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $colName
     * @param unknown $defination
     */
    protected function changeColumn( $tableName, $colName, $defination ) {
        Table::get($this->getDb(), $tableName)->changeColumn($colName, $defination);
        $this->processHandler('ChangeColumn',array(
            'tableName' => $tableName,
            'colName' => $colName,
            'defination' => $defination,
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $indexName
     * @param unknown $columns
     */
    protected function addIndex( $tableName, $indexName, $columns ) {
        Table::get($this->getDb(), $tableName)->addIndex($indexName, $columns);
        $this->processHandler('AddIndex',array(
            'tableName' => $tableName,
            'indexName' => $indexName,
            'columns' => $columns,
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $indexName
     */
    protected function dropIndex( $tableName, $indexName ) { 
        Table::get($this->getDb(), $tableName)->dropIndex($indexName);
        $this->processHandler('DropIndex',array(
            'tableName' => $tableName,
            'indexName' => $indexName,
        ));
    }
    
    /**
     * @param unknown $fkName
     * @param unknown $columns
     * @param unknown $refTable
     * @param unknown $refColumns
     */
    protected function addForginKey( $tableName, $fkName, $columns, $refTable, $refColumns ) {
        Table::get($this->getDb(), $tableName)->addForginKey($fkName, $columns, $refTable, $refColumns);
        $this->processHandler('AddForginKey',array(
            'tableName' => $tableName,
            'fkName' => $fkName,
            'columns' => $columns,
            'refTable' => $refTable,
            'refColumns' => $refColumns,
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $fkName
     */
    protected function dropForginKey( $tableName, $fkName ) {
        Table::get($this->getDb(), $tableName)->dropForginKey($fkName);
        $this->processHandler('DropForginKey',array(
            'tableName' => $tableName,
            'fkName' => $fkName,
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $condition
     * @param string $message
     */
    protected function delete( $tableName, $condition=null, $message='' ) {
        $count = Query::delete($this->getDb())->from($tableName)->where($condition)->exec();
        $this->processHandler('DeleteData',array(
            'tableName' => $tableName,
            'message' => $message,
            'count' => $count,
        ));
    }
    
    /**
     * @param unknown $tableName
     * @param unknown $data
     * @param unknown $condition
     * @param unknown $message
     */
    protected function update( $tableName, $data, $condition=null, $message='' ) {
        $count = Query::update($this->getDb())->table($tableName)->values($data)->where($condition)->exec();
        $this->processHandler('UpdateData',array(
            'tableName' => $tableName,
            'message' => $message,
            'count' => $count,
        ));
    }
    
    /**
     * @param unknown $text
     */
    protected function message( $text ) {
        $this->processHandler('Message', array(
            'text' => $text,
        ));
    }
}