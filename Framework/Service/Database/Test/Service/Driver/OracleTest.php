<?php
namespace X\Service\Database\Test\Service\Driver;
use PHPUnit\Framework\TestCase;
use X\Service\Database\DatabaseException;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
final class OracleTest extends TestCase {
    /***/
    use DatabaseServiceTestTrait;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp() {
        $this->checkTestable(TEST_DB_NAME_ORACLE);
        $this->createTestTableUser(TEST_DB_NAME_ORACLE);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->dropTestTableUser(TEST_DB_NAME_ORACLE);
    }
    
    /** test exec */
    public function test_exec() {
        $driver = $this->getDatabase(TEST_DB_NAME_ORACLE)->getDriver();
        $rowCount = $driver->exec('INSERT INTO "users" ("id","name","age","group") VALUES (1000,\'U001\', 10, \'TEST\')');
        $this->assertEquals(1, $rowCount);
        
        $rowCount = $driver->exec('DELETE FROM "users" WHERE "name"=\'U001\'');
        $this->assertEquals(1, $rowCount);
        
        for ( $i=0; $i<10; $i++ ) {
            $query = 'INSERT INTO "users" ("id","name","age") VALUES (:id, :name, :age)';
            $params = array(
                ':id' => 2000 + $i,
                ':name' => "U-{$i}",
                ':age'  => $i
            );
            $rowCount = $driver->exec($query, $params);
            $this->assertEquals(1, $rowCount);
        }
        
        try {
            $driver->exec('ERROR QUERY');
        } catch ( DatabaseException $e ) {}
    }
    
    /** test query */
    public function test_query() {
        $this->insertDemoDataIntoTableUser(TEST_DB_NAME_ORACLE);
        $driver = $this->getDatabase(TEST_DB_NAME_ORACLE)->getDriver();
        
        $result = $driver->query('SELECT * FROM "users" WHERE "group"=\'DEMO\' AND ROWNUM = 1 ORDER BY "id" ASC')->fetch();
        $this->assertEquals('U001-DM', $result['name']);
        
        try {
            $driver->query('ERROR QUERY');
        } catch ( DatabaseException $e ) {}
    }
    
    /** test_getLastInsertId */
    public function test_getLastInsertId() {
        # 不支持
    }
    
    /**  */
    public function test_tableList() {
        $driver = $this->getDatabase(TEST_DB_NAME_ORACLE)->getDriver();
        $tables = $driver->tableList();
        $this->assertTrue(in_array('users', $tables));
    }
    
    /***/
    public function test_columnList() {
        $driver = $this->getDatabase(TEST_DB_NAME_ORACLE)->getDriver();
        $columns = $driver->columnList('users');
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('age', $columns);
        $this->assertArrayHasKey('group', $columns);
    }
}