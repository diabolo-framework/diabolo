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
}