<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Query\Insert;
use X\Service\Database\Query\Update;
use X\Service\Database\Query\Delete;
class UpdateTest extends TestCase {
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
    public function test_table() {
        $update = (new Update($this->db))
            ->table('mytable')
            ->values(array('name'=>'michael'))
            ->set('age', 10);
        $this->assertEquals('UPDATE `mytable` SET `name` = :qp0, `age` = :qp1', $update->toString());
    }
    
    /** */
    public function test_exec() {
        $insert = (new Insert($this->db))
        ->table('students')
        ->values(array(
            array('name'=>'NAME-UPD-001','age'=>10),
            array('name'=>'NAME-UPD-002','age'=>10),
        ));
        $this->assertEquals(2, $insert->exec());
        
        $update = (new Update($this->db))
            ->table('students')
            ->set('name', 'NAME-UPD-001-X')
            ->where(['name'=>array('NAME-UPD-001')]);
        $this->assertEquals(1, $update->exec());
        
        $deleteCount = (new Delete($this->db))->from('students')->exec();
        $this->assertEquals(2, $deleteCount);
    }
}