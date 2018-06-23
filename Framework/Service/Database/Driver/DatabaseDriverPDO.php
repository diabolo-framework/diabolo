<?php
namespace X\Service\Database\Driver;
use X\Service\Database\QueryResult;
abstract class DatabaseDriverPDO implements DatabaseDriver {
    /** @var \PDO */
    protected $connection = false;
    
    /**
     * Config the driver
     * @param array $config
     * @return void
     */
    public function __construct( array $config=[] ) {
        foreach ( $config as $key => $value ) {
            if ( property_exists($this, $key) ) {
                $this->$key = $value;
            }
        }
        
        $this->init();
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    /** init the driver */
    protected function init() {}
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::exec()
     */
    public function exec($query, array $params = array()){
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::query()
     */
    public function query($query, array $params = array()) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return new QueryResult($stmt);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::getLastInsertId()
     */
    public function getLastInsertId($sequenceName=null) {
        return $this->connection->lastInsertId($sequenceName);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::getErrorCode()
     */
    public function getErrorCode(){
        return $this->connection->errorCode();
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::getErrorMessage()
     */
    public function getErrorMessage() {
        $error = $this->connection->errorInfo();
        return $error[2];
    }
}