<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Query\Delete;
use X\Service\Database\Query\Insert;
class DeleteTest extends TestCase {
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
        $delete = (new Delete($this->db))
            ->table('mytable');
        $this->assertEquals('DELETE FROM `mytable`', $delete->toString());
    }
    
    /** */
    public function test_exec() {
        $insert = (new Insert($this->db))
        ->table('students')
        ->values(array(
            array('name'=>'NAME-DEL-001','age'=>10),
            array('name'=>'NAME-DEL-002','age'=>10),
        ));
        $this->assertEquals(2, $insert->exec());
        
        $delete = (new Delete($this->db))
            ->table('students')
            ->where(['name'=>array('NAME-DEL-001','NAME-DEL-002')]);
        $this->assertEquals(2, $delete->exec());
    }
}