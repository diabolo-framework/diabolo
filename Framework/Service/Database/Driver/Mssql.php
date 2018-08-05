<?php
namespace X\Service\Database\Driver;
use X\Service\Database\Table\Column;
class Mssql extends DatabaseDriverPDO {
    /** @var string host address of mssql server */
    protected $host;
    /** @var string username to mssql server */
    protected $username;
    /** @var string password to mssql service */
    protected $password;
    /** @var string database name to access */
    protected $dbname;
    /** @var integer port to mssql server */
    protected $port = 1433;
    /** @var string name of charset ot server */
    protected $charset="UTF-8";
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverPDO::getOptions()
     */
    protected function getOptions() {
        return array(
            self::OPT_RENAME_COLUMN_ON_CHANGING_COLUMN => false,
        );
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverPDO::mapColumnTypeToDatabaseType()
     */
    public function mapColumnTypeToDatabaseType( $type ) {
        $map = array(
            'STRING' => 'VARCHAR',
            'INTEGER' => 'INT',
            'DECIMAL' => 'DECIMAL',
        );
        $type = strtoupper($type);
        return isset($map[$type]) ? $map[$type] : $type;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverBase::init()
     */
    protected function init() {
        $dsn = "sqlsrv:Database={$this->dbname};Server={$this->host},{$this->port}";
        $this->connection = new \PDO($dsn,$this->username,$this->password);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::quoteTableName()
     */
    public function quoteTableName($tableName) {
        return '"'.str_replace('"', '""', $tableName).'"';
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::quoteColumnName()
     */
    public function quoteColumnName($columnName) {
        return '"'.str_replace('"', '""', $columnName).'"';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::getName()
     */
    public function getName() {
        return 'mssql';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::tableList()
     */
    public function tableList() {
        $query = 'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = \'BASE TABLE\' AND TABLE_CATALOG=:dbname';
        $tables = $this->query($query, array('dbname'=>$this->dbname))->fetchAll();
        foreach ( $tables as $index => $table ) {
            $tables[$index] = $table['TABLE_NAME'];
        }
        return $tables;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::columnList()
     */
    public function columnList($tableName) {
        $query = 'select * from information_schema.columns where table_name = :tbname';
        $columns = $this->query($query, array(':tbname'=>$tableName))->fetchAll();
        
        $pkQuery = 'SELECT K.COLUMN_NAME
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS C
            JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS K
            ON C.TABLE_NAME = K.TABLE_NAME
                AND C.CONSTRAINT_CATALOG = K.CONSTRAINT_CATALOG
                AND C.CONSTRAINT_SCHEMA = K.CONSTRAINT_SCHEMA
                AND C.CONSTRAINT_NAME = K.CONSTRAINT_NAME
            WHERE C.CONSTRAINT_TYPE = \'PRIMARY KEY\'
                AND K.TABLE_NAME = :tbname';
        $primaryKeyName = $this->query($pkQuery, array(':tbname'=>$tableName))->fetchAll();
        $primaryKeyName = isset($primaryKeyName[0]) ? $primaryKeyName[0]['COLUMN_NAME'] : null;
        
        $list = array();
        foreach ( $columns as $index => $item ) {
            $column = new Column();
            $column->setName($item['COLUMN_NAME']);
            $column->setType($item['DATA_TYPE']);
            if ( !empty($item['CHARACTER_MAXIMUM_LENGTH']) ) {
                $column->setLength($item['CHARACTER_MAXIMUM_LENGTH']);
            } elseif ( !empty( $item['NUMERIC_PRECISION']) ) {
                $column->setLength($item['NUMERIC_PRECISION']);
                $column->setDecimals($item['NUMERIC_SCALE']);
            }
            $column->setIsNotNull('NO'===$item['IS_NULLABLE']);
            $column->setDefaultValue($item['COLUMN_DEFAULT']);
            $column->setIsPrimary($primaryKeyName === $item['COLUMN_NAME']);
            $list[$column->getName()] = $column;
        }
        return $list;
    }

}