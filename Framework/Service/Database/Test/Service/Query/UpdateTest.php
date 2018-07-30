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
    
    /***/
    private function doTestUpdate( $dbName ) {
        # update all
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $updateCount = Query::update($dbName)
            ->table('users')
            ->set('group', 'GROUP-UP')
            ->exec();
        $this->assertEquals($insertCount, $updateCount);
        $this->dropTestTableUser($dbName);
        
        # update one
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $updateCount = Query::update($dbName)
            ->table('users')
            ->set('group', 'GROUP-UP')
            ->limit(1)
            ->exec();
        $this->assertEquals(1, $updateCount);
        $this->dropTestTableUser($dbName);
    }
    
    /** */
    public function test_mysql() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestUpdate(TEST_DB_NAME_MYSQL);
    }
}