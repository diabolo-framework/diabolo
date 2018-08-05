<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
class UpdateTest extends TestCase {
    /***/
    use DatabaseServiceTestTrait;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->cleanAllDatabase();
    }
    
    /***/
    private function doTestUpdateAll( $dbName ) {
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $groupUpCountBefor = Query::select($dbName)->from('users')->where(['group'=>'GROUP-UP'])->all()->count();
        $this->assertEquals(0, $groupUpCountBefor);
        $updateCount = Query::update($dbName)
            ->table('users')
            ->set('group', 'GROUP-UP')
            ->exec();
        $this->assertEquals($insertCount, $updateCount);
        $groupUpCountAfter = Query::select($dbName)->from('users')->where(['group'=>'GROUP-UP'])->all()->count();
        $this->assertEquals($insertCount, $groupUpCountAfter);
        $this->dropTestTableUser($dbName);
    }
    
    /**
     * @param unknown $dbName
     */
    private function doTestUpdateLimit($dbName) {
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $updateQuery = Query::update($dbName)
            ->table('users')
            ->set('group', 'GROUP-UP')
            ->limit(1);
        if ( 'postgresql' === $this->getDatabase($dbName)->getDriver()->getName() ) {
            $updateQuery->setPrimaryKeyName('id');
        }
        $updateCount = $updateQuery->exec();
        $this->assertEquals(1, $updateCount);
        $groupUpCountAfter = Query::select($dbName)->from('users')->where(['group'=>'GROUP-UP'])->all()->count();
        $this->assertEquals(1, $groupUpCountAfter);
        $this->dropTestTableUser($dbName);
    }
    
    /** */
    public function test_mysql_updateAll() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestUpdateAll(TEST_DB_NAME_MYSQL);
    }
    
    /** */
    public function test_mysql_updateLimit() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestUpdateLimit(TEST_DB_NAME_MYSQL);
    }
    
    /** */
    public function test_sqlite_updateAll() {
        $this->checkTestable(TEST_DB_NAME_SQLITE);
        $this->doTestUpdateAll(TEST_DB_NAME_SQLITE);
    }
    
    /** */
    public function test_sqlite_updateLimit() {
        $this->checkTestable(TEST_DB_NAME_SQLITE);
        $this->doTestUpdateLimit(TEST_DB_NAME_SQLITE);
    }
    
    /** */
    public function test_postgresql_updateAll() {
        $this->checkTestable(TEST_DB_NAME_POSTGRESQL);
        $this->doTestUpdateAll(TEST_DB_NAME_POSTGRESQL);
    }
    
    /** */
    public function test_postgresql_updateLimit() {
        $this->checkTestable(TEST_DB_NAME_POSTGRESQL);
        $this->doTestUpdateLimit(TEST_DB_NAME_POSTGRESQL);
    }
    
    /** */
    public function test_oracle_updateAll() {
        $this->checkTestable(TEST_DB_NAME_ORACLE);
        $this->doTestUpdateAll(TEST_DB_NAME_ORACLE);
    }
    
    /** */
    public function test_oracle_updateLimit() {
        $this->checkTestable(TEST_DB_NAME_ORACLE);
        $this->doTestUpdateLimit(TEST_DB_NAME_ORACLE);
    }
    
    /** */
    public function test_mssql_updateAll() {
        $this->checkTestable(TEST_DB_NAME_MSSQL);
        $this->doTestUpdateAll(TEST_DB_NAME_MSSQL);
    }
    
    /** */
    public function test_mssql_updateLimit() {
        $this->checkTestable(TEST_DB_NAME_MSSQL);
        $this->doTestUpdateLimit(TEST_DB_NAME_MSSQL);
    }
}