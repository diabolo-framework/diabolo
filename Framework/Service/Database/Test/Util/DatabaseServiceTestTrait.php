<?php
namespace X\Service\Database\Test\Util;
use X\Service\Database\DatabaseException;
use X\Service\Database\Service;
trait DatabaseServiceTestTrait {
    use DatabaseServiceMysqlTestTrait,
        DatabaseServiceSqliteTestTrait,
        DatabaseServicePostgresqlTestTrait,
        DatabaseServiceOracleTestTrait,
        DatabaseServiceMssqlTestTrait,
        DatabaseServiceFirebirdTestTrait;
    
    /** 
     * @param string $dbName
     * @return void
     */
    protected function createTestTableUser( $dbName ) {
        switch ( $this->getDatabase($dbName)->getDriver()->getName() ) {
        case 'mysql' : $this->createTestTableUserForMysql($dbName); break;
        case 'sqlite' : $this->createTestTableUserForSqlite($dbName); break;
        case 'postgresql' : $this->createTestTableUserForPostgresql($dbName); break; 
        case 'oracle' : $this->createTestTableUserForOracle($dbName); break;
        case 'mssql' : $this->createTestTableUserForMssql($dbName); break;
        case 'firebird' : $this->createTestTableUserForFirebird($dbName); break;
        }
    }
    
    /**
     * @param string $dbName
     */
    protected function insertDemoDataIntoTableUser( $dbName ) {
        $name = $this->getDatabase($dbName)->getDriver()->getName();
        $handler = 'insertDemoDataIntoTableUserFor'.ucfirst($name);
        return $this->$handler($dbName);
    }
    
    /**
     * @param string $dbName
     */
    protected function dropTestTableUser( $dbName ) {
        switch ( $this->getDatabase($dbName)->getDriver()->getName() ) {
        case 'mysql' : $this->dropTestTableUserForMysql($dbName); break;
        case 'sqlite' : $this->dropTestTableUserForSqlite($dbName); break;
        case 'postgresql' : $this->dropTestTableUserForPostgresql($dbName); break;
        case 'oracle' : $this->dropTestTableUserForOracle($dbName); break;
        case 'mssql' : $this->dropTestTableUserForMssql($dbName); break;
        case 'firebird' : $this->dropTestTableUserForFirebird($dbName); break;
        }
        
        Service::getService()->reloadDB($dbName);
    }
    
    /**
     * @param string $dbName
     * @return \X\Service\Database\Database
     */
    protected function getDatabase( $dbName ) {
        return Service::getService()->getDB($dbName);
    }
    
    /**
     * @param string $dbName
     * @throws DatabaseException
     */
    protected function checkTestable( $dbName ) {
        if ( !Service::getService()->hasDB($dbName) ) {
            throw new DatabaseException("`{$dbName}` is not configed for testing");
        }
    }
    
    /***/
    protected function cleanAllDatabase() {
        try {
            $this->dropTestTableUser(TEST_DB_NAME_MYSQL);
        } catch ( \Exception $e ) {}
        try {
            $this->dropTestTableUser(TEST_DB_NAME_SQLITE);
        } catch ( \Exception $e ) {}
        try {
            $this->dropTestTableUser(TEST_DB_NAME_POSTGRESQL);
        } catch ( \Exception $e ) {}
        try {
            $this->dropTestTableUser(TEST_DB_NAME_ORACLE);
        } catch ( \Exception $e ) {}
        try {
            $this->dropTestTableUser(TEST_DB_NAME_MSSQL);
        } catch ( \Exception $e ) {}
    }
}