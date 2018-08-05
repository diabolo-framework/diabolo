<?php
namespace X\Service\Database\Test\Util;
trait DatabaseServiceMssqlTestTrait {
    /**
     * @param string $dbName
     * @return \X\Service\Database\Database
     */
    abstract protected function getDatabase( $dbName );
    
    /**
     * create test user table for mysql
     * @return void
     */
    protected function createTestTableUserForMssql( $dbName ) {
        $this->getDatabase($dbName)->exec('CREATE TABLE [users](
            [id] [int] IDENTITY(1,1) NOT NULL,
            [name] [varchar](64) NULL,
            [age] [int] NULL,
            [group] [varchar](64) NULL,
            PRIMARY KEY CLUSTERED  (
                [id] ASC
            )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
            ) ON [PRIMARY]'
        );
    }
    
    /**
     * drop test user table
     * @param unknown $dbName
     */
    protected function dropTestTableUserForMssql ( $dbName ) {
        $this->getDatabase($dbName)->exec("DROP TABLE [users]");
    }
    
    /**
     * @param unknown $dbName
     */
    protected function insertDemoDataIntoTableUserForMssql ( $dbName ) {
        return $this->getDatabase($dbName)->exec('
            INSERT INTO [users] ([name],[age],[group]) 
            VALUES (\'U001-DM\', 10, \'DEMO\'),
                   (\'U002-DM\', 20, \'DEMO\'),
                   (\'U003-DM\', 30, \'DEMO\'),
                   (\'U004-DM\', 30, \'DEMO\'),
                   (\'U005-DM\', 30, \'DEMO2\'),
                   (\'U006-DM\', 30, \'DEMO2\')
        ');
    }
}