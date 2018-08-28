<?php 
namespace X\Service\Database\Test\Resource\Migration;
use X\Service\Database\Migration\Migration;
class test_migration_003 extends Migration {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\Migration::up()
     */
    public function up() {
        $this->createTable('table001', array(
            'id' => 'VARCHAR(32)',
            'name' => 'VARCHAR(255)',
        ));
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\Migration::down()
     */
    public function down() {
        $this->dropTable('table001');
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\Migration::getDb()
     */
    protected function getDb() {
        return 'MyDatabaseName';
    }
}