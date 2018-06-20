<?php
namespace X\Service\Database\Driver;
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
}