<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
class TruncateTableTest extends TestCase {
    /***/
    use DatabaseServiceTestTrait;
    
    /***/
    private function doTestTruncateTable( $dbName ) {
        # truncate
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        Query::truncateTable($dbName)->table('users')->exec();
        $totalCount = Query::select($dbName)->from('users')->all()->count();
        $this->assertEquals(0, $totalCount);
        $this->dropTestTableUser($dbName);
    }
    
    /** */
    public function test_mysql() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestTruncateTable(TEST_DB_NAME_MYSQL);
    }
}