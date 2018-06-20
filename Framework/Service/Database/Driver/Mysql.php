<?php
namespace X\Service\Database\Driver;
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
    public function quoteColumnName($columnName) {
        return '`'.str_replace('`', '``', $columnName).'`';
    }
}