<?php
namespace X\Service\Database\Test\Service\Driver;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\DatabaseException;
final class PostgreSQLTest extends TestCase {
    /***/
    use DatabaseServiceTestTrait;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp() {
        $this->checkTestable(TEST_DB_NAME_POSTGRESQL);
        $this->createTestTableUser(TEST_DB_NAME_POSTGRESQL);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->dropTestTableUser(TEST_DB_NAME_POSTGRESQL);
    }
    
    /** test exec */
    public function test_exec() {
        $driver = $this->getDatabase(TEST_DB_NAME_POSTGRESQL)->getDriver();
        $rowCount = $driver->exec(
            'INSERT INTO users ("name","age","group")
            VALUES (\'U001\', 10, \'TEST\'),(\'U002\', 20, \'TEST\')');
        $this->assertEquals(2, $rowCount);
        
        $rowCount = $driver->exec('DELETE FROM users WHERE name=\'U001\'');
        $this->assertEquals(1, $rowCount);
        
        for ( $i=0; $i<10; $i++ ) {
            $query = 'INSERT INTO users (name,age) VALUES (:name, :age)';
            $params = array(
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
        $this->insertDemoDataIntoTableUser(TEST_DB_NAME_POSTGRESQL);
        $driver = $this->getDatabase(TEST_DB_NAME_POSTGRESQL)->getDriver();
        
        $result = $driver->query('SELECT * FROM users where "group"=\'DEMO\' ORDER BY id ASC LIMIT 1')->fetch();
        $this->assertEquals('U001-DM', $result['name']);
        
        try {
            $driver->query('ERROR QUERY');
        } catch ( DatabaseException $e ) {}
    }
    
    /** test_getLastInsertId */
    public function test_getLastInsertId() {
        $this->insertDemoDataIntoTableUser(TEST_DB_NAME_POSTGRESQL);
        $driver = $this->getDatabase(TEST_DB_NAME_POSTGRESQL)->getDriver();
        $lastInsertId = $driver->getLastInsertId();
        
        $driver->exec('INSERT INTO users (name,age) VALUES (\'iddd\', 10)');
        $this->assertEquals($lastInsertId+1, $driver->getLastInsertId());
    }
    
    /**  */
    public function test_tableList() {
        $driver = $this->getDatabase(TEST_DB_NAME_POSTGRESQL)->getDriver();
        $tables = $driver->tableList();
        $this->assertTrue(in_array('users', $tables));
    }
    
    /***/
    public function test_columnList() {
        $driver = $this->getDatabase(TEST_DB_NAME_POSTGRESQL)->getDriver();
        $columns = $driver->columnList('users');
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('age', $columns);
        $this->assertArrayHasKey('group', $columns);
    }
}