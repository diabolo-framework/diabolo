<?php
namespace X\Service\KeyValue\Test\Service\Storage;
use PHPUnit\Framework\TestCase;
use X\Service\KeyValue\Storage\Memcached;
class MemcachedTest extends TestCase {
    /***/
    public function test_database() {
        $storage = new Memcached(array(
            'host' => '127.0.0.1',
            'port' => 11211,
            'prefix' => 'DIABOLO_',
        ));
        
        # set/get
        $storage->set('K001', 'V001');
        $this->assertEquals('V001', $storage->get('K001'));
        
        $storage->set('K002', 'V002', array(Memcached::KEYOPT_EXPIRE_AT => 2));
        $this->assertEquals('V002', $storage->get('K002'));
        sleep(3);
        $this->assertEquals(null, $storage->get('K002'));
        
        # exists / delete / rename
        $storage->set('K001', 'V001');
        $this->assertTrue($storage->exists('K001'));
        $storage->delete('K001');
        $this->assertFalse($storage->exists('K001'));
        $storage->set('K001', 'V001');
        $storage->rename('K001', 'K00X');
        $this->assertFalse($storage->exists('K001'));
        $this->assertTrue($storage->exists('K00X'));
    }
}