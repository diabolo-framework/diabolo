<?php
namespace X\Service\Database\Test\Service;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Migrate;
use X\Service\Database\Service;
use X\Service\Database\Migration\HistoryHandler\File;
class MigrationTest extends TestCase {
    /** */
    public function test_migrate() {
        $migrate = new Migrate(array(
            'namespace' => '\\X\\Service\\Database\\Test\\Resource\\Migration',
            'path' => Service::getService()->getPath('Test/Resource/Migration'),
            'history' => array(
                'class' => File::class,
            )
        ));
        
        $migrate->up(1);
        $migrate->up(2);
        $migrate->up(3);
        
        $migrate->down(1);
        $migrate->down(1);
        $migrate->down(1);
    }
}