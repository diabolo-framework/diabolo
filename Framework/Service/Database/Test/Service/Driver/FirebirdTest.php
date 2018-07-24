<?php
namespace X\Service\Database\Test\Service\Driver;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\DatabaseException;
final class FirebirdTest extends TestCase {
    /***/
    use DatabaseServiceTestTrait;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp() {
        $this->checkTestable(TEST_DB_NAME_FIREBIRD);
        $this->createTestTableUser(TEST_DB_NAME_FIREBIRD);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->dropTestTableUser(TEST_DB_NAME_FIREBIRD);
    }
    
    /** test exec */
    public function test_exec() {
        $driver = $this->getDatabase(TEST_DB_NAME_FIREBIRD)->getDriver();
        $rowCount = $driver->exec(
            'INSERT INTO USERS ("NAME","AGE","GROUP")
            VALUES (\'U001\', 10, \'TEST\')');
        $this->assertEquals(1, $rowCount);
        
        $rowCount = $driver->exec('DELETE FROM USERS WHERE NAME=\'U001\'');
        $this->assertEquals(1, $rowCount);
        
        for ( $i=0; $i<10; $i++ ) {
            $query = 'INSERT INTO USERS (NAME,AGE) VALUES (:name, :age)';
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
        $this->insertDemoDataIntoTableUser(TEST_DB_NAME_FIREBIRD);
        $driver = $this->getDatabase(TEST_DB_NAME_FIREBIRD)->getDriver();
        
        $result = $driver->query('SELECT FIRST 1 * FROM USERS where "GROUP"=\'DEMO\' ORDER BY id ASC')->fetch();
        $this->assertEquals('U001-DM', $result['NAME']);
        
        try {
            $driver->query('ERROR QUERY');
        } catch ( DatabaseException $e ) {}
    }
    
    /** test_getLastInsertId */
    public function test_getLastInsertId() {
        # DO NOTHING
    }
    
    /**  */
    public function test_tableList() {
        $driver = $this->getDatabase(TEST_DB_NAME_FIREBIRD)->getDriver();
        $tables = $driver->tableList();
        $this->assertTrue(in_array('USERS', $tables));
    }
    
    /***/
    public function test_columnList() {
        $driver = $this->getDatabase(TEST_DB_NAME_FIREBIRD)->getDriver();
        $columns = $driver->columnList('USERS');
        $this->assertArrayHasKey('ID', $columns);
        $this->assertArrayHasKey('NAME', $columns);
        $this->assertArrayHasKey('AGE', $columns);
        $this->assertArrayHasKey('GROUP', $columns);
    }
}