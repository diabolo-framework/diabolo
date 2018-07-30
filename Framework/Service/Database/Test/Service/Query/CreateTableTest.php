<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
use X\Service\Database\Table\Column;
class CreateTableTest extends TestCase {
    /***/
    use DatabaseServiceTestTrait;
    
    /***/
    private function doTestCreateTable( $dbName ) {
        Query::createTable($dbName)
            ->name('new_table')
            ->addColumn((new Column())->setName('col1')->setType('VARCHAR')->setLength(100))
            ->exec();
        $this->assertTrue(in_array('new_table', $this->getDatabase($dbName)->tableList()));
        Query::dropTable($dbName)->table('new_table')->exec();
        $this->assertFalse(in_array('new_table', $this->getDatabase($dbName)->tableList()));
    }
    
    /** */
    public function test_mysql() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestCreateTable(TEST_DB_NAME_MYSQL);
    }
}