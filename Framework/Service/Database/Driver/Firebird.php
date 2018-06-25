<?php
namespace X\Service\Database\Driver;
/**
 * @todo no truncate
 * @todo can not insert mutil rows
 * @todo table name and column name is upper case
 */
class Firebird extends DatabaseDriverPDO {
    /** @var string host address of mssql server */
    protected $host = 'localhost';
    /** @var integer port to mssql server */
    protected $port = 3050;
    /** @var string username to mssql server */
    protected $username = null;
    /** @var string password to mssql service */
    protected $password = null;
    /** @var string database name to access */
    protected $dbname;
    /** @var string name of charset ot server */
    protected $charset="UTF-8";
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverBase::init()
     */
    protected function init() {
        $dsn = "firebird:dbname={$this->host}/{$this->port}:{$this->dbname}";
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
}