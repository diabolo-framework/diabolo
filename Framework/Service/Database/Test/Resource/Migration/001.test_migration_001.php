<?php 
namespace X\Service\Database\Test\Resource\Migration;
use X\Service\Database\Migration\Migration;
class test_migration_001 extends Migration {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\Migration::up()
     */
    public function up() {
        
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\Migration::down()
     */
    public function down() {
        echo "DOWN DOWN DOWN";
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\Migration::getDb()
     */
    protected function getDb() {
        return 'default';
    }
}