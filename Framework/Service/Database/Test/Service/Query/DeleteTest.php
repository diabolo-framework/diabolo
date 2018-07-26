<?php
namespace X\Service\Database\Test\Service\Query;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Query\Delete;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
class DeleteTest extends TestCase {
    /** Uses */
    use DatabaseServiceTestTrait;
    
    /**
     * @param unknown $dbName
     */
    private function doTestDelete( $dbName, $tableName ) {
        # delete all
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $delCount = Query::delete($dbName)->from($tableName)->exec();
        $this->assertEquals($insertCount, $delCount, 'failed to delete all');
        $this->dropTestTableUser($dbName);
        
        # delete one
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $delCount = Query::delete($dbName)->from($tableName)->limit(1)->exec();
        $rowCount = Query::select($dbName)->from($tableName)->all()->count();
        $this->assertEquals(1, $delCount, 'failed to delete one on assert $delCount');
        $this->assertEquals($insertCount-1, $rowCount, 'failed to delete one on assert $rowCount');
        $this->dropTestTableUser($dbName);
        
        # delete ten
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $delCount = Query::delete($dbName)->from($tableName)->limit(10)->exec();
        $this->assertEquals($insertCount, $delCount, 'failed to delete ten');
        $this->dropTestTableUser($dbName);
        
        # delete with condition
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $this->assertEquals(1, Query::select($dbName)->from($tableName)->where(['name'=>'U001-DM'])->all()->count());
        $delCount = Query::delete($dbName)->from($tableName)->where(['name'=>'U001-DM'])->exec();
        $this->assertEquals(1, $delCount, 'failed to delete with condition');
        $this->assertEquals(0, Query::select($dbName)->from($tableName)->where(['name'=>'U001-DM'])->all()->count());
        $this->dropTestTableUser($dbName);
        
        # delete with order
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $minId = Query::select($dbName)->from($tableName)->orderBy('id',SORT_ASC)->one();
        $delCount = Query::delete($dbName)->from($tableName)->orderBy('id',SORT_ASC)->limit(1)->exec();
        $this->assertEquals(1, $delCount, 'failed to delete with order');
        $deletedMinId = Query::select($dbName)->from($tableName)->orderBy('id',SORT_ASC)->one();
        $this->assertLessThan($deletedMinId['id'],$minId['id']);
        $this->dropTestTableUser($dbName);
    }
    
    /** */
    public function test_mysql() {
        $this->doTestDelete(TEST_DB_NAME_MYSQL, 'users');
    }
    
    /** */
    public function test_sqlite() {
        $this->doTestDelete(TEST_DB_NAME_SQLITE, 'users');
    }
}