<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Query\Condition;
use X\Service\Database\Service;
class ConditionTest extends TestCase {
    private $db = null;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp() {
        $this->db = Service::getService()->getDB(TEST_DB_NAME_MYSQL);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->db = null;
    }
    
    /** */
    public function test_group() {
        $condition = Condition::build()
            ->add(Condition::build()->is('name', 'michael')->is('sex', 'm'))
            ->or()
            ->add(Condition::build()->is('name','lois')->is('sex', 'f'));
        $condition->setDatabase($this->db);
        $this->assertEquals('( ( `name` = :qp0 AND `sex` = :qp1 ) OR ( `name` = :qp2 AND `sex` = :qp3 ) )', $condition->toString());
        $this->assertEquals(array (
            ':qp0' => 'michael',
            ':qp1' => 'm',
            ':qp2' => 'lois',
            ':qp3' => 'f',
        ), $condition->getBindParams());
    }
    
    /**  */
    public function test_connectors() {
        # mutil conditions
        $condition = Condition::build()
            ->is('name', 'michael')
            ->lessOrEqual('age', 10)
            ->is('sex', 'm');
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` = :qp0 AND `age` <= :qp1 AND `sex` = :qp2 )', $condition->toString());
        $this->assertEquals(array(
            ':qp0' => 'michael',
            ':qp1' => 10,
            ':qp2' => 'm'
        ), $condition->getBindParams());
        
        
        # simply and or
        $condition = Condition::build()
            ->is('name', 'michael')
            ->and()
            ->is('sex', 'm')
            ->or()
            ->is('age', 10);
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` = :qp0 AND `sex` = :qp1 OR `age` = :qp2 )', $condition->toString());
        $this->assertEquals(array(
            ':qp0' => 'michael',
            ':qp1' => 'm',
            ':qp2' => 10,
        ), $condition->getBindParams());
    }
    
    /**  */
    public function test_custom_condition() {
        # string
        $condition = Condition::build()->add('age=1');
        $condition->setDatabase($this->db);
        $this->assertEquals('( age=1 )', $condition->toString());
        
        # array 
        $condition = Condition::build()->add(array(
            'name' => 'michael',
        ));
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` = :qp0 )', $condition->toString());
        $this->assertEquals(array(
            ':qp0' => 'michael',
        ), $condition->getBindParams());
        
        # Condition object
        $conditionTmp = Condition::build()->is('name', 'michael');
        $condition = Condition::build()->add($conditionTmp);
        $condition->setDatabase($this->db);
        $this->assertEquals('( ( `name` = :qp0 ) )', $condition->toString());
        $this->assertEquals(array(
            ':qp0' => 'michael',
        ), $condition->getBindParams());
    }
    
    /** */
    public function test_operators() {
        # =
        $condition = Condition::build()->is('name', 'michael');
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` = :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>'michael'), $condition->getBindParams());
        
        # <>
        $condition = Condition::build()->isNot('name', 'michael');
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` <> :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>'michael'), $condition->getBindParams());
        
        # <
        $condition = Condition::build()->lessThan('age', 10);
        $condition->setDatabase($this->db);
        $this->assertEquals('( `age` < :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>10), $condition->getBindParams());
        
        # <=
        $condition = Condition::build()->lessOrEqual('age', 10);
        $condition->setDatabase($this->db);
        $this->assertEquals('( `age` <= :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>10), $condition->getBindParams());
        
        # >
        $condition = Condition::build()->greaterThan('age', 10);
        $condition->setDatabase($this->db);
        $this->assertEquals('( `age` > :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>10), $condition->getBindParams());
        
        # >=
        $condition = Condition::build()->greaterOrEqual('age', 10);
        $condition->setDatabase($this->db);
        $this->assertEquals('( `age` >= :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>10), $condition->getBindParams());
        
        # contains
        $condition = Condition::build()->contains('name', 'michael');
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` LIKE :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>'%michael%'), $condition->getBindParams());
        
        # not contains
        $condition = Condition::build()->notContains('name', 'michael');
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` NOT LIKE :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>'%michael%'), $condition->getBindParams());
        
        # beginWith
        $condition = Condition::build()->beginWith('name', 'michael');
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` LIKE :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>'michael%'), $condition->getBindParams());
        
        # endWith
        $condition = Condition::build()->endWith('name', 'michael');
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` LIKE :qp0 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>'%michael'), $condition->getBindParams());
        
        # isNUll
        $condition = Condition::build()->isNull('name');
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` IS NULL )', $condition->toString());
        $this->assertEquals(array(), $condition->getBindParams());
        
        # isNotNull
        $condition = Condition::build()->isNotNull('name');
        $condition->setDatabase($this->db);
        $this->assertEquals('( `name` IS NOT NULL )', $condition->toString());
        $this->assertEquals(array(), $condition->getBindParams());
        
        # between
        $condition = Condition::build()->between('age', 10, 20);
        $condition->setDatabase($this->db);
        $this->assertEquals('( `age` BETWEEN :qp0 AND :qp1 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>10,':qp1'=>20), $condition->getBindParams());
        
        # not between
        $condition = Condition::build()->notBetween('age', 10, 20);
        $condition->setDatabase($this->db);
        $this->assertEquals('( `age` NOT BETWEEN :qp0 AND :qp1 )', $condition->toString());
        $this->assertEquals(array(':qp0'=>10,':qp1'=>20), $condition->getBindParams());
        
        # in
        $condition = Condition::build()->in('age', array(10, 20, 30));
        $condition->setDatabase($this->db);
        $this->assertEquals('( `age` IN ( :qp0, :qp1, :qp2 ) )', $condition->toString());
        $this->assertEquals(array(':qp0'=>10,':qp1'=>20,':qp2'=>30), $condition->getBindParams());
        
        # notin
        $condition = Condition::build()->notIn('age', array(10, 20, 30));
        $condition->setDatabase($this->db);
        $this->assertEquals('( `age` NOT IN ( :qp0, :qp1, :qp2 ) )', $condition->toString());
        $this->assertEquals(array(':qp0'=>10,':qp1'=>20,':qp2'=>30), $condition->getBindParams());
    }
}