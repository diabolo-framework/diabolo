<?php
namespace X\Service\Database\Driver;
use X\Service\Database\Table\Column;

class Sqlite extends DatabaseDriverPDO {
    /** @var string file path to sqlite database */
    protected $path = ':memory:';
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverPDO::init()
     */
    protected function init() {
        $dsn = "sqlite:{$this->path}";
        $this->connection = new \PDO($dsn);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::quoteTableName()
     */
    public function quoteTableName($tableName) {
        return '`'.str_replace('`', '``', $tableName).'`';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::quoteColumnName()
     */
    public function quoteColumnName($columnName) {
        return '`'.str_replace('`', '``', $columnName).'`';
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::getName()
     */
    public function getName() {
        return 'sqlite';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::tableList()
     */
    public function tableList() {
        $tables = $this->query('SELECT * FROM sqlite_master WHERE type="table"')->fetchAll();
        foreach ( $tables as $index => $table ) {
            $tables[$index] = $table['tbl_name'];
        }
        return $tables;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::columnList()
     */
    public function columnList($tableName) {
        $tableNameAsValue = $tableName;
        $tableName = $this->quoteTableName($tableName);
        $columns = $this->query('PRAGMA TABLE_INFO('.$tableName.')')->fetchAll();
        
        $indexs = $this->query('PRAGMA index_list('.$tableName.')')->fetchAll();
        $uniqueColumns = array();
        foreach ( $indexs as $index ) {
            if ( 0 === $index['unique'] ) {
                continue;
            }
            
            $indexColumns = $this->query('PRAGMA index_info('.$index['name'].')')->fetchAll();
            foreach ( $indexColumns as $indexColumn ) {
                $uniqueColumns[] = $indexColumn['name'];
            }
        }
        
        $hasAutoIncrease = 0 < $this
            ->query('SELECT * FROM sqlite_master WHERE type = "table" AND name = :table AND sql LIKE "%AUTOINCREMENT%"',array(':table'=>$tableNameAsValue))
            ->count();
        $list = array();
        foreach ( $columns as $item ) {
            $column = new Column();
            $column->setName($item['name']);
            $column->setType($item['type']);
            $column->setIsNotNull( "1" === $item['notnull'] );
            $column->setIsPrimary( "1" === $item['pk'] );
            $column->setDefaultValue($item['dflt_value']);
            $column->setIsUnique(in_array($item['name'], $uniqueColumns));
            
            if ( 'INTEGER' === strtoupper($item['type']) 
            && "1" === $item['pk'] 
            && $hasAutoIncrease ) {
                $column->setIsAutoIncrement(true);
            }
            $list[$column->getName()] = $column;
        }
        return $list;
    }
}