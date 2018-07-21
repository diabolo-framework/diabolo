<?php
namespace X\Service\Database\Test\Util;
trait DatabaseServiceOracleTestTrait {
    /**
     * @param string $dbName
     * @return \X\Service\Database\Database
     */
    abstract protected function getDatabase( $dbName );
    
    /**
     * create test user table for mysql
     * @return void
     */
    protected function createTestTableUserForOracle( $dbName ) {
        $this->getDatabase($dbName)->exec('CREATE TABLE "users" (
          "id" NUMBER NOT NULL ,
          "name" VARCHAR2(255) NOT NULL ,
          "age" NUMBER ,
          "group" VARCHAR2(255) ,
          PRIMARY KEY ("id")
        )');
    }
    
    /**
     * drop test user table
     * @param unknown $dbName
     */
    protected function dropTestTableUserForOracle ( $dbName ) {
        $this->getDatabase($dbName)->exec('DROP TABLE "users"');
    }
    
    /**
     * @param unknown $dbName
     */
    protected function insertDemoDataIntoTableUserForOracle ( $dbName ) {
        $this->getDatabase($dbName)->exec('INSERT INTO "users" ("id","name","age","group") VALUES (1,\'U001-DM\', 10, \'DEMO\')');
        $this->getDatabase($dbName)->exec('INSERT INTO "users" ("id","name","age","group") VALUES (2,\'U002-DM\', 20, \'DEMO\')');
        $this->getDatabase($dbName)->exec('INSERT INTO "users" ("id","name","age","group") VALUES (3,\'U003-DM\', 30, \'DEMO\')');
        $this->getDatabase($dbName)->exec('INSERT INTO "users" ("id","name","age","group") VALUES (4,\'U004-DM\', 30, \'DEMO\')');
        $this->getDatabase($dbName)->exec('INSERT INTO "users" ("id","name","age","group") VALUES (5,\'U005-DM\', 30, \'DEMO2\')');
        $this->getDatabase($dbName)->exec('INSERT INTO "users" ("id","name","age","group") VALUES (6,\'U006-DM\', 30, \'DEMO2\')');
    }
}