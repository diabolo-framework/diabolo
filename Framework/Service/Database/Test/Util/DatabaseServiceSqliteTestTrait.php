<?php
namespace X\Service\Database\Test\Util;
trait DatabaseServiceSqliteTestTrait {
    /**
     * @param string $dbName
     * @return \X\Service\Database\Database
     */
    abstract protected function getDatabase( $dbName );
    
    /**
     * create test user table for sqlite
     * @return void
     */
    protected function createTestTableUserForSqlite( $dbName ) {
        $this->getDatabase($dbName)->exec('CREATE TABLE `users` (
            `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
            `name`  TEXT NOT NULL,
            `age`   INTEGER,
            `group` TEXT
        )');
    }
    
    /**
     * drop test user table
     * @param unknown $dbName
     */
    protected function dropTestTableUserForSqlite ( $dbName ) {
        $this->getDatabase($dbName)->exec("DROP TABLE `users`");
    }
    
    /**
     * @param unknown $dbName
     */
    protected function insertDemoDataIntoTableUserForSqlite ( $dbName ) {
        $this->getDatabase($dbName)->exec('
            INSERT INTO users (`name`,`age`,`group`) 
            VALUES ("U001-DM", 10, "DEMO"),
                   ("U002-DM", 20, "DEMO"),
                   ("U003-DM", 30, "DEMO"),
                   ("U004-DM", 30, "DEMO"),
                   ("U005-DM", 30, "DEMO2"),
                   ("U006-DM", 30, "DEMO2")
        ');
    }
}