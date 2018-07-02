<?php
namespace X\Service\Database\Test\Service;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Query;
use X\Service\Database\Query\Select;
use X\Service\Database\Query\Insert;
use X\Service\Database\Query\Delete;
use X\Service\Database\Query\Update;
class QueryTest extends TestCase {
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
    public function test_select() {
        $this->assertTrue(Query::select($this->db) instanceof Select);
    }
    
    /** */
    public function test_insert() {
        $this->assertTrue(Query::insert($this->db) instanceof Insert);
    }
    
    /** */
    public function test_delete() {
        $this->assertTrue(Query::delete($this->db) instanceof Delete);
    }
    
    /** */
    public function test_update() {
        $this->assertTrue(Query::update($this->db) instanceof Update);
    }
}