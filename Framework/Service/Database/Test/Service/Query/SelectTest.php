<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Query\Select;
use X\Service\Database\Database;
use X\Service\Database\Query\Expression;
use X\Service\Database\Query\Condition;
class SelectTest extends TestCase {
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
    
    /***/
    public function test_expression() {
        $select = new Select($this->db);
        $this->assertEquals('SELECT *', $select->toString());
        
        $select = new Select($this->db);
        $select->expression('Num','num');
        $select->expression('Name', 'name');
        $this->assertEquals('SELECT `Num` AS `num`, `Name` AS `name`', $select->toString());
        
        $select = new Select($this->db);
        $select->expression(new Expression('1+1'), 'CusExpr');
        $this->assertEquals('SELECT 1+1 AS `CusExpr`', $select->toString());
        
        $select = new Select($this->db);
        $select->expression((new Select($this->db)), 'SubQueryResult');
        $this->assertEquals('SELECT ( SELECT * ) AS `SubQueryResult`', $select->toString());
    }
    
    /**  */
    public function test_from() {
        $select = new Select($this->db);
        $select->from('mytable', 'MYTABLE');
        $this->assertEquals('SELECT * FROM `mytable` `MYTABLE`', $select->toString());
    }
    
    /**  */
    public function test_where() {
        $select = new Select($this->db);
        $select->from('mytable', 'MYTABLE');
        $select->where(Condition::build()->is('name', 'michael'));
        $this->assertEquals('SELECT * FROM `mytable` `MYTABLE` WHERE ( `name` = :qp0 )', $select->toString());
        
        $select = new Select($this->db);
        $select->from('mytable', 'MYTABLE');
        $select->where('1=1');
        $this->assertEquals('SELECT * FROM `mytable` `MYTABLE` WHERE ( 1=1 )', $select->toString());
    }
    
    /** */
    public function test_groupBy() {
        $select = new Select($this->db);
        $select->from('mytable')
            ->groupBy('name')
            ->groupBy('age');
        $this->assertEquals('SELECT * FROM `mytable` GROUP BY `name`, `age`', $select->toString());
    }
    
    /***/
    public function test_having() {
        $select = (new Select($this->db))
            ->from('mytable')
            ->where(Condition::build()->notBetween('age', 10, 12))
            ->groupBy('name')
            ->having(Condition::build()->greaterThan(Expression::count(), 1));
        $this->assertEquals('SELECT * FROM `mytable` WHERE ( `age` NOT BETWEEN :qp0 AND :qp1 ) GROUP BY `name` HAVING ( COUNT(*) > :qp2 )', $select->toString());
    }
    
    /***/
    public function test_join() {
        $select = (new Select($this->db))
            ->from('mytable')
            ->join(Select::INNER_JOIN, 'another_table', Condition::build()->is('id', Expression::column('mid', $this->db)), 'table_alias')
            ->join(Select::LEFT_JOIN, 'x_table', '1=1');
        $this->assertEquals('SELECT * FROM `mytable` INNER JOIN `another_table` `table_alias` ON ( `id` = `mid` ) LEFT JOIN `x_table` ON ( 1=1 )', $select->toString());
    }
    
    /***/
    public function test_orderBy() {
        $select = (new Select($this->db))
            ->from('mytable')
            ->orderBy('id', Select::SORT_DESC)
            ->orderBy('created', Select::SORT_ASC);
        $this->assertEquals('SELECT * FROM `mytable` ORDER BY `id` DESC, `created` ASC', $select->toString());
    }
    
    /***/
    public function test_limit() {
        $select = (new Select($this->db))
            ->from('mytable')
            ->limit(1);
        $this->assertEquals('SELECT * FROM `mytable` LIMIT 1', $select->toString());
    }
    
    /***/
    public function test_offset() {
        $select = (new Select($this->db))
            ->from('mytable')
            ->offset(1);
        $this->assertEquals('SELECT * FROM `mytable` OFFSET 1', $select->toString());
    }
    
    /***/
    public function test_all() {
        $select = (new Select($this->db));
        $select->from('query_test');
        $result = $select->all()->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals($result[0]['id'], 1);
        $this->assertEquals($result[1]['id'], 2);
    }
    
    /***/
    public function test_one() {
        $select = (new Select($this->db));
        $select->from('query_test')->orderBy('id', Select::SORT_ASC);
        $result = $select->one();
        $this->assertEquals($result['id'], 1);
    }
}