<?php
namespace X\Service\Database\Test\Util;
use X\Service\Database\DatabaseException;
use X\Service\Database\Service;
trait DatabaseServiceTestTrait {
    use DatabaseServiceMysqlTestTrait;
    
    /** 
     * @param string $dbName
     * @return void
     */
    protected function createTestTableUser( $dbName ) {
        switch ( $this->getDatabase($dbName)->getDriver()->getName() ) {
        case 'mysql' : $this->createTestTableUserForMysql($dbName); break;
        }
    }
    
    /**
     * @param string $dbName
     */
    protected function insertDemoDataIntoTableUser( $dbName ) {
        switch ( $this->getDatabase($dbName)->getDriver()->getName() ) {
        case 'mysql' : $this->insertDemoDataIntoTableUserForMysql($dbName); break;
        }
    }
    
    /**
     * @param string $dbName
     */
    protected function dropTestTableUser( $dbName ) {
        switch ( $this->getDatabase($dbName)->getDriver()->getName() ) {
        case 'mysql' : $this->dropTestTableUserForMysql($dbName); break;
        }
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
}