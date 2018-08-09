<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
use X\Service\Database\Table\Column;
use X\Service\Database\Driver\DatabaseDriver;
class CreateTableTest extends TestCase {
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
    private function doTestCreateTable( $dbName ) {
        Query::createTable($dbName)
            ->name('new_table')
            ->addColumn((new Column())->setName('col1')->setType(Column::T_STRING)->setLength(100))
            ->exec();
        
        $tableName = 'new_table';
        if ( $this->getDatabase($dbName)->getDriver()->getOption(DatabaseDriver::OPT_UPPERCASE_TABLE_NAME, false) ) {
            $tableName = strtoupper($tableName);
        }
        $this->assertTrue(in_array($tableName, $this->getDatabase($dbName)->tableList()));
        Query::dropTable($dbName)->table($tableName)->exec();
        $this->assertFalse(in_array($tableName, $this->getDatabase($dbName)->tableList()));
    }
    
    /** */
    public function test_mysql() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestCreateTable(TEST_DB_NAME_MYSQL);
    }
    
    /** */
    public function test_sqlite() {
        $this->checkTestable(TEST_DB_NAME_SQLITE);
        $this->doTestCreateTable(TEST_DB_NAME_SQLITE);
    }
    
    /** */
    public function test_postgresql() {
        $this->checkTestable(TEST_DB_NAME_POSTGRESQL);
        $this->doTestCreateTable(TEST_DB_NAME_POSTGRESQL);
    }
    
    /** */
    public function test_oracle() {
        $this->checkTestable(TEST_DB_NAME_ORACLE);
        $this->doTestCreateTable(TEST_DB_NAME_ORACLE);
    }
    
    /** */
    public function test_mssql() {
        $this->checkTestable(TEST_DB_NAME_MSSQL);
        $this->doTestCreateTable(TEST_DB_NAME_MSSQL);
    }
    
    /** */
    public function test_firebird() {
        $this->checkTestable(TEST_DB_NAME_FIREBIRD);
        $this->doTestCreateTable(TEST_DB_NAME_FIREBIRD);
    }
}