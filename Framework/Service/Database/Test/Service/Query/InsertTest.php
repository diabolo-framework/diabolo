<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Query\Insert;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
class InsertTest extends TestCase {
    /***/
    use DatabaseServiceTestTrait;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->cleanAllDatabase();
    }
    
    /**
     * @param unknown $dbName
     */
    private function doTestInsert( $dbName ) {
        # test insert one row
        $this->createTestTableUser($dbName);
        $totalCount = Query::select($dbName)->from('users')->all()->count();
        $insertCount = Query::insert($dbName)->table('users')->value(array(
            'name' => 'INS-0001',
            'age' => 100,
            'group' => 'INS_T'
        ))->exec();
        $newTotalCount = Query::select($dbName)->from('users')->all()->count();
        $this->assertEquals(1, $insertCount);
        $this->assertEquals($totalCount+$insertCount, $newTotalCount);
        $this->dropTestTableUser($dbName);
        
        # test insert tow rows
        $this->createTestTableUser($dbName);
        $totalCount = Query::select($dbName)->from('users')->all()->count();
        $insertCount = Query::insert($dbName)->table('users')->values(array(
            array('name' => 'INS-000X','age' => 100,'group' => 'INS_T'),
            array('name' => 'INS-000Y','age' => 100,'group' => 'INS_T'),
        ))->exec();
        $newTotalCount = Query::select($dbName)->from('users')->all()->count();
        $this->assertEquals(2, $insertCount);
        $this->assertEquals($totalCount+$insertCount, $newTotalCount);
        $this->dropTestTableUser($dbName);
    }
    
    /** */
    public function test_mysql() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestInsert(TEST_DB_NAME_MYSQL);
    }
    
    /** */
    public function test_sqlite() {
        $this->checkTestable(TEST_DB_NAME_SQLITE);
        $this->doTestInsert(TEST_DB_NAME_SQLITE);
    }
    
    /** */
    public function test_postgresql() {
        $this->checkTestable(TEST_DB_NAME_POSTGRESQL);
        $this->doTestInsert(TEST_DB_NAME_POSTGRESQL);
    }
}