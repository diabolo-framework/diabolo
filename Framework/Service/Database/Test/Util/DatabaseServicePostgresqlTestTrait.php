<?php
namespace X\Service\Database\Test\Util;
trait DatabaseServicePostgresqlTestTrait {
    /**
     * @param string $dbName
     * @return \X\Service\Database\Database
     */
    abstract protected function getDatabase( $dbName );
    
    /**
     * create test user table for Postgresql
     * @return void
     */
    protected function createTestTableUserForPostgresql( $dbName ) {
        $this->getDatabase($dbName)->exec('CREATE TABLE "users" (
            "id"    SERIAL PRIMARY KEY NOT NULL,
            "name"  CHARACTER VARYING,
            "age"   INTEGER,
            "group" CHARACTER VARYING
        )');
    }
    
    /**
     * drop test user table
     * @param unknown $dbName
     */
    protected function dropTestTableUserForPostgresql ( $dbName ) {
        $this->getDatabase($dbName)->exec('DROP TABLE "users"');
    }
    
    /**
     * @param unknown $dbName
     */
    protected function insertDemoDataIntoTableUserForPostgresql ( $dbName ) {
        $this->getDatabase($dbName)->exec('
            INSERT INTO "users" ("name","age","group") 
            VALUES (\'U001-DM\', 10, \'DEMO\'),
                   (\'U002-DM\', 20, \'DEMO\'),
                   (\'U003-DM\', 30, \'DEMO\'),
                   (\'U004-DM\', 30, \'DEMO\'),
                   (\'U005-DM\', 30, \'DEMO2\'),
                   (\'U006-DM\', 30, \'DEMO2\')
        ');
        return 6;
    }
}