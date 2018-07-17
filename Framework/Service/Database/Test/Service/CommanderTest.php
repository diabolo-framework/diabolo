<?php
namespace X\Service\Database\Test\Service;
use X\Core\X;
use X\Service\Database\Database;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Service;
use X\Service\Database\Commander;
use X\Service\Database\Command\Command;
class CommanderTest extends TestCase {
    private $db = null;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp() {
        $config = X::system()->getConfiguration()->get('params')->get('MysqlDriverConfig');
        $db = new Database($config);
        $this->db = $db;
        
        Commander::addPath(Service::getService()->getPath('Test/Resource/Command'));
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->db = null;
    }
    
    /**  */
    public function test_getCommand() {
        $command = Commander::getCommand('student.search', $this->db);
        $this->assertTrue($command instanceof Command);
    }
    
    public function test_exec() {
        $command = Commander::getCommand('student.testMixedUp', $this->db);
        $command->query(['conditions'=>['name'=>'michael','age'=>10, 'falseTest'=>false]])->count();
        
        $command = Commander::getCommand('student.testStartByToken', $this->db);
        $command->query(['value'=>'SELECT'])->count();
        
        $command = Commander::getCommand('student.testIf', $this->db);
        $command->query(['age'=>10, 'name'=>'xxx', 'class'=>1000, 'maxClass'=>10])->count();
        
        $command = Commander::getCommand('student.testForeach', $this->db);
        $command->query(['conditions'=>['name'=>'michael','age'=>10]])->count();
        
        $command = Commander::getCommand('student.testPlaceHolder', $this->db);
        $command->query(['table'=>'students'])->count();
        
        $command = Commander::getCommand('student.searchByName', $this->db);
        $command->query(['name'=>'michael'])->count();
        
        $command = Commander::getCommand('student.search', $this->db);
        $command->exec(array('name'=>'michael'));
    }
}