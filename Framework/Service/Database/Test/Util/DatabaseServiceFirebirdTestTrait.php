<?php
namespace X\Service\Database\Test\Util;
trait DatabaseServiceFirebirdTestTrait {
    /**
     * @param string $dbName
     * @return \X\Service\Database\Database
     */
    abstract protected function getDatabase( $dbName );
    
    /**
     * create test user table for Firebird
     * @return void
     */
    protected function createTestTableUserForFirebird( $dbName ) {
        $this->getDatabase($dbName)->exec('CREATE TABLE USERS (
          ID       INTEGER NOT NULL PRIMARY KEY,
          NAME     VARCHAR(20),
          AGE      INTEGER,
          "GROUP"  VARCHAR(20)
        )');
        $this->getDatabase($dbName)->exec('CREATE GENERATOR GEN_USERS_ID;');
        $this->getDatabase($dbName)->exec('CREATE TRIGGER USERS_BI FOR USERS
            ACTIVE BEFORE INSERT POSITION 0
            AS
            BEGIN
              IF (NEW.ID IS NULL) THEN
                NEW.ID = GEN_ID(GEN_USERS_ID,1);
            END'
        );
    }
    
    /**
     * drop test user table
     * @param unknown $dbName
     */
    protected function dropTestTableUserForFirebird ( $dbName ) {
        $this->getDatabase($dbName)->exec('DROP TABLE "USERS"');
        $this->getDatabase($dbName)->exec('DROP SEQUENCE GEN_USERS_ID');
    }
    
    /**
     * @param unknown $dbName
     */
    protected function insertDemoDataIntoTableUserForFirebird ( $dbName ) {
        $this->getDatabase($dbName)->exec('INSERT INTO USERS ("NAME","AGE","GROUP") VALUES (\'U001-DM\', 10, \'DEMO\')');
        $this->getDatabase($dbName)->exec('INSERT INTO USERS ("NAME","AGE","GROUP") VALUES (\'U002-DM\', 20, \'DEMO\')');
        $this->getDatabase($dbName)->exec('INSERT INTO USERS ("NAME","AGE","GROUP") VALUES (\'U003-DM\', 30, \'DEMO\')');
        $this->getDatabase($dbName)->exec('INSERT INTO USERS ("NAME","AGE","GROUP") VALUES (\'U004-DM\', 30, \'DEMO\')');
        $this->getDatabase($dbName)->exec('INSERT INTO USERS ("NAME","AGE","GROUP") VALUES (\'U005-DM\', 30, \'DEMO2\')');
        $this->getDatabase($dbName)->exec('INSERT INTO USERS ("NAME","AGE","GROUP") VALUES (\'U006-DM\', 30, \'DEMO2\')');
    }
}