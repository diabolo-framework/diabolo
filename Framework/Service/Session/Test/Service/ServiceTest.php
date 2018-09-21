<?php 
namespace X\Service\Session\Test\Service;
use PHPUnit\Framework\TestCase;
use X\Service\Session\Service;
class ServiceTest extends TestCase {
    public function test_service() {
        $service = Service::getService();
        $service->start();
        
        $service->startSession();
        $this->assertEquals(null, $service->get('TEST_01'));
        $service->set('TEST_02', "TV02");
        $this->assertEquals('TV02', $service->get('TEST_02'));
        $service->closeSession();
    }
}