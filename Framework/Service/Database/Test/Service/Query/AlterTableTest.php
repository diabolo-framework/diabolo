<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
class AlterTableTest extends TestCase {
    /***/
    use DatabaseServiceTestTrait;
    
    /***/
    private function doTestAlterTable( $dbName ) {
        # rename
        $this->createTestTableUser($dbName);
        Query::alterTable($dbName)->table('users')->rename('new_users')->exec();
        $this->assertTrue(in_array('new_users', $this->getDatabase($dbName)->tableList()));
        Query::dropTable($dbName)->table('new_users')->exec();
        
        # addColumn
        $this->createTestTableUser($dbName);
        Query::alterTable($dbName)->table('users')->addColumn('newCol', 'TEXT')->exec();
        $this->assertArrayHasKey('newCol', $this->getDatabase($dbName)->columnList('users'));
        $this->dropTestTableUser($dbName);
        
        # drop column
        $this->createTestTableUser($dbName);
        Query::alterTable($dbName)->table('users')->dropColumn('name')->exec();
        $this->assertArrayNotHasKey('name', $this->getDatabase($dbName)->columnList('users'));
        $this->dropTestTableUser($dbName);
        
        # changeColumn
        $this->createTestTableUser($dbName);
        Query::alterTable($dbName)->table('users')->changeColumn('name','TEXT')->exec();
        $this->assertArrayHasKey('name', $this->getDatabase($dbName)->columnList('users'));
        $this->dropTestTableUser($dbName);
    
        # add/drop index
        $this->createTestTableUser($dbName);
        Query::alterTable($dbName)->table('users')->addIndex('idx_001', array('name'))->exec();
        Query::alterTable($dbName)->table('users')->dropIndex('idx_001')->exec();
        $this->assertTrue(true);
    }
    
    /** */
    public function test_mysql() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestAlterTable(TEST_DB_NAME_MYSQL);
    }
}