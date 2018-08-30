Migration
=========
migration use to manage database struct, it's controllable and able to roll back.

Create migration script
-----------------------

the quick way to create a migration file is using the follwing command : ::

    $ diabolo service/database/migration/create create_table_table001

then you can find the migration file in ``Migration`` folder under your applition root.

here is an example of migration script : ::

    <?php 
    namespace X\Service\Database\Test\Resource\Migration;
    use X\Service\Database\Migration\Migration;
    class test_migration_003 extends Migration {
        public function up() {
            $this->createTable('table001', array(
                'id VARCHAR(32)',
                'name VARCHAR(255)',
            ));
        }

        public function down() {
            $this->dropTable('table001');
        }
        
        protected function getDb() {
            return 'MyDatabaseName';
        }
    }

Execute migrate
---------------

the following commands use to update/rollback the database : ::

    # update database to the newest
    $ diabolo service/database/migration/up 
    
    # update database with step count
    $ diabolo service/database/migration/up 1
    
    # rollback database with step count
    $ diabolo service/database/migration/down 1
    
    # rollback database to last version
    $ diabolo service/database/migration/down


