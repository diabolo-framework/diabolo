<?php
namespace X\Service\Database;
use X\Core\Service\XService;
class Service extends XService {
    /** @var array database config array */
    protected $databases = array();
    /** @var Database[] */
    protected $databaseInstances = array();
    
    /**
     * @param string|mixed $db
     * @return \X\Service\Database\Database
     */
    public function getDB( $db ) {
        if ( $db instanceof Database ) {
            return $db;
        }
        if ( isset($this->databaseInstances[$db]) ) {
            return $this->databaseInstances[$db];
        }
        if ( !isset($this->databases[$db]) ) {
            throw new DatabaseException("can not find database config `{$db}`");
        }
        $config = $this->databases[$db];
        $this->databaseInstances[$db] = new Database($config);
        return $this->databaseInstances[$db];
    }
    
    /**
     * @param string $db
     * @return boolean
     */
    public function hasDB( $dbName ) {
        return isset($this->databases[$dbName]);
    }
    
    /**
     * @param string $dbName
     */
    public function reloadDB( $dbName ) {
        if ( isset($this->databaseInstances[$dbName]) ) {
            unset($this->databaseInstances[$dbName]);
            $this->getDB($dbName);
        }
    }
}