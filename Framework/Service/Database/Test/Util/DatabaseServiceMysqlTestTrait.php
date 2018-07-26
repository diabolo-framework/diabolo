<?php
namespace X\Service\Database\Test\Util;
trait DatabaseServiceMysqlTestTrait {
    /**
     * @param string $dbName
     * @return \X\Service\Database\Database
     */
    abstract protected function getDatabase( $dbName );
    
    /**
     * create test user table for mysql
     * @return void
     */
    protected function createTestTableUserForMysql( $dbName ) {
        $this->getDatabase($dbName)->exec('CREATE TABLE `users` (
            `id`  int(11) NOT NULL AUTO_INCREMENT ,
            `name`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
            `age`  int(255) NULL DEFAULT NULL ,
            `group`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB
              DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
              AUTO_INCREMENT=1
              ROW_FORMAT=DYNAMIC'
        );
    }
    
    /**
     * drop test user table
     * @param unknown $dbName
     */
    protected function dropTestTableUserForMysql ( $dbName ) {
        $this->getDatabase($dbName)->exec("DROP TABLE `users`");
    }
    
    /**
     * @param unknown $dbName
     */
    protected function insertDemoDataIntoTableUserForMysql ( $dbName ) {
        $this->getDatabase($dbName)->exec('
            INSERT INTO users (`name`,`age`,`group`) 
            VALUES ("U001-DM", 10, "DEMO"),
                   ("U002-DM", 20, "DEMO"),
                   ("U003-DM", 30, "DEMO"),
                   ("U004-DM", 30, "DEMO"),
                   ("U005-DM", 30, "DEMO2"),
                   ("U006-DM", 30, "DEMO2")
        ');
        return 6;
    }
}