<?php
namespace X\Service\Database\Test\Service\Driver;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Driver\Mysql;
final class MysqlTest extends TestCase {
    /** @var Mysql */
    private $driver = null;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp() {
        $config = X::system()->getConfiguration()->get('params')->get('MysqlDriverConfig');
        $this->driver = new Mysql($config);
        
        $this->driver->exec('INSERT INTO students (name,age) VALUES ("michael", 10)');
        $this->driver->exec('INSERT INTO students (name,age) VALUES ("lois", 20)');
        $this->driver->exec('INSERT INTO students (name,age) VALUES ("lana", 30)');
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->driver->exec('TRUNCATE students');
        $this->driver = null;
    }
    
    /** test exec */
    public function test_exec() {
        $this->driver->exec('TRUNCATE students');
        
        $rowCount = $this->driver->exec('INSERT INTO students (name,age) VALUES ("michael", 10),("lois", 20)');
        $this->assertEquals(2, $rowCount);
        
        $rowCount = $this->driver->exec('DELETE FROM students WHERE name="michael"');
        $this->assertEquals(1, $rowCount);
        
        for ( $i=0; $i<10; $i++ ) {
            $query = 'INSERT INTO students (name,age) VALUES (:name, :age)';
            $params = array(
                ':name' => "stu-{$i}",
                ':age'  => $i
            );
            $rowCount = $this->driver->exec($query, $params);
            $this->assertEquals(1, $rowCount);
        }
    }
    
    /** test query */
    public function test_query() {
        $result = $this->driver->query('SELECT * FROM students where name="michael"')->fetch();
        $this->assertEquals('michael', $result['name']);
    }
    
    /** test_getLastInsertId */
    public function test_getLastInsertId() {
        $this->assertEquals(3, $this->driver->getLastInsertId());
        
        $this->driver->exec('INSERT INTO students (name,age) VALUES ("iddd", 10)');
        $this->assertEquals(4, $this->driver->getLastInsertId());
    }
}