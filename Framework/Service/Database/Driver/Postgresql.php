<?php
namespace X\Service\Database\Driver;
use OAuth2\Storage\Pdo;

class Postgresql extends DatabaseDriverPDO {
    /** @var string host address of mysql server */
    protected $host;
    /** @var string username to mysql server */
    protected $username;
    /** @var string password to mysql service */
    protected $password;
    /** @var string database name to access */
    protected $dbname;
    /** @var integer port to mysql server */
    protected $port = 5432;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverPDO::init()
     */
    protected function init() {
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
        $this->connection = new \PDO($dsn, $this->username, $this->password);
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