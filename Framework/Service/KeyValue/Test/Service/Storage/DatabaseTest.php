<?php
namespace X\Service\KeyValue\Test\Service\Storage;
use PHPUnit\Framework\TestCase;
use X\Service\KeyValue\Storage\Database;
class DatabaseTest extends TestCase {
    /***/
    public function test_database() {
        $storage = new Database(array(
            'dbname' => 'kvdbname',
            'tableName' => 'keyvalue',
            'prefix' => 'diabolo_',
        ));
        
        # clean / size
        $storage->clean();
        $this->assertEquals(0, $storage->size());
        $storage->set('K001', 'V001');
        $this->assertEquals(1, $storage->size());
        
        # match
        $storage->clean();
        $this->assertEquals(array(), $storage->match('K00'));
        $storage->set('K001', 'V001');
        $this->assertEquals(array('diabolo_K001'), $storage->match('K00'));
        $storage->set('K001X', 'V00X');
        $this->assertEquals(array('diabolo_K001','diabolo_K001X'), $storage->match('K00'));
        
        # set/get
        $storage->set('K001', 'V001');
        $this->assertEquals('V001', $storage->get('K001'));
        
        $storage->set('K002', 'V002', array(Database::KEYOPT_EXPIRE_AT => 2));
        $this->assertEquals('V002', $storage->get('K002'));
        sleep(3);
        $this->assertEquals(null, $storage->get('K002'));
        
        # clean / size
        $storage->clean();
        $this->assertEquals(0, $storage->size());
        $storage->set('K001', 'V001');
        $this->assertEquals(1, $storage->size());
        
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