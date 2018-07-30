<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
class DropTableTest extends TestCase {
    /***/
    use DatabaseServiceTestTrait;
    
    /***/
    private function doTestDropTable( $dbName ) {
        # truncate
        $this->createTestTableUser($dbName);
        $this->assertTrue(in_array('users', $this->getDatabase($dbName)->tableList()));
        Query::dropTable($dbName)->table('users')->exec();
        $this->assertFalse(in_array('users', $this->getDatabase($dbName)->tableList()));
    }
    
    /** */
    public function test_mysql() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestDropTable(TEST_DB_NAME_MYSQL);
    }
}