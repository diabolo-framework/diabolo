<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Query\Insert;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
use X\Service\Database\Driver\DatabaseDriver;
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
        $rowData = array(
            'name' => 'INS-0001',
            'age' => 100,
            'group' => 'INS_T'
        );
        if ( !$this->getDatabase($dbName)->getDriver()->getOption(DatabaseDriver::OPT_AUTO_INCREASE_ON_INSERT, true) ) {
            $rowData['id'] = 1;
        }
        $insertCount = Query::insert($dbName)->table('users')->value($rowData)->exec();
        $newTotalCount = Query::select($dbName)->from('users')->all()->count();
        $this->assertEquals(1, $insertCount);
        $this->assertEquals($totalCount+$insertCount, $newTotalCount);
        $this->dropTestTableUser($dbName);
        
        # test insert tow rows
        $this->createTestTableUser($dbName);
        $totalCount = Query::select($dbName)->from('users')->all()->count();
        $rowsData = array(
            array('name' => 'INS-000X','age' => 100,'group' => 'INS_T'),
            array('name' => 'INS-000Y','age' => 100,'group' => 'INS_T'),
        );
        if ( !$this->getDatabase($dbName)->getDriver()->getOption(DatabaseDriver::OPT_AUTO_INCREASE_ON_INSERT, true) ) {
            $rowsData[0]['id'] = 1;
            $rowsData[1]['id'] = 2;
        }
        $insertCount = Query::insert($dbName)->table('users')->values($rowsData)->exec();
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
    
    /** */
    public function test_oracle() {
        $this->checkTestable(TEST_DB_NAME_ORACLE);
        $this->doTestInsert(TEST_DB_NAME_ORACLE);
    }
    
    /** */
    public function test_mssql() {
        $this->checkTestable(TEST_DB_NAME_MSSQL);
        $this->doTestInsert(TEST_DB_NAME_MSSQL);
    }
}