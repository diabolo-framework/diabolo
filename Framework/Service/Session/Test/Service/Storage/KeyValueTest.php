<?php
namespace X\Service\Session\Test\Service\Storage;
use PHPUnit\Framework\TestCase;
use X\Service\Session\Storage\KeyValue;
class KeyValueTest extends TestCase {
    public function test_Storage() {
        $storage = new KeyValue(array(
            'storageName' => 'sesionStorage',
            'lifetime' => 10,
        ));
        $sessionId = 'TEST_SESSION_ID';
        
        $storage->open(null,null);
        $this->assertTrue($storage->destroy($sessionId));
        $this->assertEquals('', $storage->read($sessionId));
        $this->assertTrue($storage->write($sessionId, 'TEST_SESSION_DATA'));
        $this->assertEquals('TEST_SESSION_DATA', $storage->read($sessionId));
        sleep(15);
        $this->assertEquals('', $storage->read($sessionId));
        $this->assertTrue($storage->write($sessionId, 'TEST_SESSION_DATA'));
        $storage->destroy($sessionId);
        $this->assertEquals('', $storage->read($sessionId));
        $storage->gc(null);
        $storage->close();
    }
}