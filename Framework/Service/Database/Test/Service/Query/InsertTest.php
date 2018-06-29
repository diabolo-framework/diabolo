<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Query\Insert;
class InsertTest extends TestCase {
    /** @var Database */
    private $db = null;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp() {
        $config = X::system()->getConfiguration()->get('params')->get('MysqlDriverConfig');
        $db = new Database($config);
        $this->db = $db;
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->db = null;
    }
    
    /** */
    public function test_toString () {
        $insert = new Insert($this->db);
        $insert->table('mytable')->value(array(
            'name' => '铁柱',
            'age' => 10,
        ));
        $this->assertEquals('INSERT INTO `mytable` ( `name`, `age` ) VALUES ( :qp0, :qp1 )', $insert->toString());
        
        $insert = new Insert($this->db);
        $insert->table('mytable')->values(array(
            array('name' => '铁柱','age' => 10,),
            array('name' => '二妞','age' => 10,),
        ));
        $this->assertEquals('INSERT INTO `mytable` ( `name`, `age` ) VALUES ( :qp0, :qp1 ),( :qp2, :qp3 )', $insert->toString());
    }
    
    /** */
    public function test_exec() {
        $this->db->exec('TRUNCATE TABLE students');
        
        $insert = new Insert($this->db);
        $insert->table('students')->value(array(
            'name' => '铁柱-001',
            'age' => 10,
        ));
        $this->assertEquals(1, $insert->exec());
        
        $insert = new Insert($this->db);
        $insert->table('students')->values(array(
            array('name' => '铁柱-002','age' => 10,),
            array('name' => '二妞-002','age' => 10,),
        ));
        $this->assertEquals(2, $insert->exec());
        
        $this->db->exec('TRUNCATE TABLE students');
    }
}