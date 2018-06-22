<?php
namespace X\Service\Database\Driver;
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
    protected $port = 10060;
    /** @var string name of charset ot server */
    protected $charset="UTF-8";
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverBase::init()
     */
    protected function init() {
        $prefix = (false===strpos(strtoupper(PHP_OS), 'WIN')) ? 'dblib' : 'mssql';
        $dsn = "{$prefix}:dbname={$this->dbname};host={$this->host}:{$this->port}";
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