<?php
namespace X\Service\Session\Test\Service;
use PHPUnit\Framework\TestCase;
use X\Service\Session\SessionFlash;
use X\Service\Session\Service;
class SessionFlashTest extends TestCase {
    public function test_flash() {
        Service::getService()->start();
        
        $this->assertFalse(SessionFlash::has('TEST001'));
        SessionFlash::set('TEST001', 'VAL001');
        $this->assertTrue(SessionFlash::has('TEST001'));
        $this->assertEquals('VAL001', SessionFlash::get('TEST001'));
        $this->assertFalse(SessionFlash::has('TEST001'));
    }
}