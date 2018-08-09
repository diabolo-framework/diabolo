<?php
namespace X\Service\Database\Driver;
use X\Service\Database\Table\Column;

class Mysql extends DatabaseDriverPDO {
    /** @var string host address of mysql server */
    protected $host;
    /** @var string username to mysql server */
    protected $username;
    /** @var string password to mysql service */
    protected $password;
    /** @var string database name to access */
    protected $dbname;
    /** @var integer port to mysql server */
    protected $port = 3306;
    /** @var string chatset name */
    protected $charset = 'UTF8';
    
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
     * @see \X\Service\Database\Driver\DatabaseDriver::getName()
     */
    public function getName() {
        return 'mysql';
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverBase::init()
     */
    protected function init() {
        $dsn = "mysql:dbname={$this->dbname};host={$this->host};port={$this->port}";
        $this->connection = new \PDO($dsn,$this->username,$this->password);
        $this->connection->exec('SET NAMES '.$this->charset);
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
    public function quoteColumnName($columnName, $options=array()) {
        return '`'.str_replace('`', '``', $columnName).'`';
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::tableList()
     */
    public function tableList() {
        $list = $this->query('SHOW TABLES')->fetchAll();
        foreach ( $list as $index => $item ) {
            $list[$index] = array_pop($item);
        }
        return $list;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::columnList()
     */
    public function columnList($tableName) {
        $query = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = :table AND TABLE_SCHEMA = :dbname";
        $columns = $this->query($query, array(':table'=>$tableName, ':dbname'=>$this->dbname))->fetchAll();
        
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
            $column->setIsAutoIncrement(false !== strpos($item['EXTRA'], 'auto_increment'));
            $column->setIsUnique(false !== strpos($item['COLUMN_KEY'], 'UNI'));
            $column->setIsPrimary(false !== strpos($item['COLUMN_KEY'], 'PRI'));
            $column->setComment($item['COLUMN_COMMENT']);
            
            $list[$column->getName()] = $column;
        }
        return $list;
    }
}