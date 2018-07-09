<?php
namespace X\Service\Database;
use X\Service\Database\Table\Column;
class Table {
    /** @var string */
    private $name = null;
    /** @var Database */
    private $database = null;
    
    /**
     * @param Database|string $db
     * @return self[]
     */
    public static function all( $db ) {
        $db = Service::getService()->getDB($db);
        $tableList = $db->tableList();
        foreach ( $tableList as $index => $tableName ) {
            $tableList[$index] = new self($db, $tableName);
        }
        return $tableList;
    }
    
    /**
     * @param Database|string $db
     * @param string $name
     */
    public static function get($db, $name) {
        $db = Service::getService()->getDB($db);
        $tableList = $db->tableList();
        if ( !in_array($name, $tableList) ) {
            return  null;
        }
        return new self($db, $name);
    }
    
    /**
     * @param Database|string $db
     * @param string $name
     * @param string|Column $columns
     */
    public static function create($db, $name, $columns) {
        $query = Query::createTable($db)->name($name);
        foreach ( $columns as $column ) {
            $query->addColumn($column);
        }
        $query->exec();
        return new self($db, $name);
    }
    
    /**
     * @param Database $db
     * @param string $name
     */
    public function __construct( $db, $name ) {
        $this->database = $db;
        $this->name = $name;
    }
    
    /** @return void */
    public function drop() {
        Query::dropTable($this->database)->table($this->name)->exec();
    }
    
    /** @return self */
    public function truncate() {
        Query::truncateTable($this->database)->table($this->name)->exec();
        return $this;
    }
    
    /** @return self */
    public function rename($newName) {
        Query::alterTable($this->database)->table($this->name)->rename($newName)->exec();
        $this->name = $newName;
        return $this;
    }
    
    /**
     * @param unknown $name
     * @param string|Column $defination
     * @return self
     */
    public function addColumn($name, $defination){
        if ( $defination instanceof Column ) {
            $defination->setDatabase($this->database);
        }
        Query::alterTable($this->database)
            ->table($this->name)
            ->addColumn($name, $defination)
            ->exec();
        return $this; 
    }
    
    /**
     * @param string $name
     * @return self
     */
    public function dropColumn( $name ){
        Query::alterTable($this->database)
            ->table($this->name)
            ->dropColumn($name)
            ->exec();
        return $this;
    }
    
    /** @return Column[] */
    public function getColumns() {
        return $this->database->columnList($this->name);
    }
    
    /**
     * @param unknown $name
     * @param unknown $newName
     * return self
     */
    public function renameColumn( $name, $newName ) {
        $columns = $this->getColumns();
        $columns[$name]->setName(null);
        
        Query::alterTable($this->database)
            ->table($this->name)
            ->changeColumn($name, $columns[$name], $newName)
            ->exec();
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $defination
     * @return self
     */
    public function changeColumn( $name, $defination ) {
        Query::alterTable($this->database)
            ->table($this->name)
            ->changeColumn($name, $defination)
            ->exec();
        return $this;
    }
    
    /**
     * @param string $name
     * @param array $columns
     * @return self
     */
    public function addIndex( $name, $columns ) {
        Query::alterTable($this->database)
            ->table($this->name)
            ->addIndex($name, $columns)
            ->exec();
        return $this;
    }
    
    /**
     * @param unknown $name
     * @return self
     */
    public function dropIndex( $name ) {
        Query::alterTable($this->database)
            ->table($this->name)
            ->dropIndex($name)
            ->exec();
        return $this;
    }
    
    /**
     * @param unknown $name
     * @param unknown $columns
     * @param unknown $refTable
     * @param unknown $refColumns
     * @return self
     */
    public function addForginKey( $name, $columns, $refTable, $refColumns ) {
        Query::alterTable($this->database)
            ->table($this->name)
            ->addForeignKey($name, $columns, $refTable, $refColumns)
            ->exec();
        return $this;
    }
    
    /**
     * @param unknown $name
     * @return self
     */
    public function dropForginKey( $name ) {
        Query::alterTable($this->database)
            ->table($this->name)
            ->dropForeignKey($name)
            ->exec();
        return $this;
    }
}