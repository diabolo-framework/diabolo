<?php
namespace X\Service\Database\Driver;
use X\Service\Database\DatabaseException;
/**
 * @notice 值必须要使用单引号括起来
 * @notice 不支持lastInsertId， 需要使用seq获取唯一值
 * @notice 不支持多行插入，需要手工实现
 */
class Oracle extends DatabaseDriverPDO {
    /** @var string host address of oracle server */
    protected $host;
    /** @var string username to oracle server */
    protected $username;
    /** @var string password to oracle service */
    protected $password;
    /** @var string serviceName name to access */
    protected $serviceName;
    /** @var integer port to oracle server */
    protected $port = 1521;
    /** @var string chatset name */
    protected $charset = 'UTF8';
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverBase::init()
     */
    protected function init() {
        $tns = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST={$this->host})(PORT={$this->port})))".
                "(CONNECT_DATA=(SERVICE_NAME={$this->serviceName})))";
        $this->connection = new \PDO('oci:dbname='.$tns,$this->username,$this->password);
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
     * @see \X\Service\Database\Driver\DatabaseDriver::getLastInsertId()
     */
    public function getLastInsertId($sequenceName=null) {
        throw new DatabaseException('database driver does not suport this function');
    }
}