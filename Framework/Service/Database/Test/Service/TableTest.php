<?php
namespace X\Service\Database\Test\Service;
use X\Core\X;
use X\Service\Database\Database;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Table;
use X\Service\Database\Test\Resource\Model\Student;
use X\Service\Database\Table\Column;
class TableTest extends TestCase {
    private $db = null;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp() {
        $config = X::system()->getConfiguration()->get('params')->get('MysqlDriverConfig');
        $db = new Database($config);
        $this->db = $db;
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->db = null;
    }
    
    /** */
    public function test_all() {
        $tables = Table::all($this->db);
        $this->assertEquals(Table::class, get_class($tables[0]));
    }
    
    /** */
    public function test_create() {
        $table = Table::create($this->db, 'T0001', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        $this->assertNotNull(Table::get($this->db, 'T0001'));
        $table->drop();
    }
    
    /***/
    public function test_truncate() {
        $student1 = new Student();
        $student1->name = 'AR-001';
        $student1->age = 10;
        $this->assertTrue($student1->save());
        
        Table::get($this->db, Student::tableName())->truncate();
        $this->assertEquals(0, Student::findAll()->count());
    }
    
    /***/
    public function test_rename() {
        $table = Table::create($this->db, 'T0001', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        $this->assertNotNull(Table::get($this->db, 'T0001'));
        
        $table->rename('T0002');
        $this->assertNotNull(Table::get($this->db, 'T0002'));
        
        $table->drop();
    }
    
    /** */
    public function test_addColumn() {
        $table = Table::create($this->db, 'T0001', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        $this->assertNotNull(Table::get($this->db, 'T0001'));
        
        $table->addColumn('COL1', 'VARCHAR(255)');
        $table->addColumn('COL2', Column::build()
            ->setType(Column::T_DATETIME)
            ->setAfterColumn('id')
            ->setComment('测试备注""""')
            ->setDefaultValue('2018-08-08 12:20:22')
            ->setIsNotNull(true));
        
        
        $columns = $table->getColumns();
        $this->assertTrue(array_key_exists('COL1', $columns));
        $this->assertTrue(array_key_exists('COL2', $columns));
        
        $table->drop();
    }
    
    /** */
    public function test_dropColumn() {
        $table = Table::create($this->db, 'T0001', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        $this->assertNotNull(Table::get($this->db, 'T0001'));
        $table->dropColumn('name');
        $table->drop();
    }
    
    /***/
    public function test_renameColumn() {
        $table = Table::create($this->db, 'T0001', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        $this->assertNotNull(Table::get($this->db, 'T0001'));
        $table->renameColumn('name', 'NewName');
        $this->assertTrue(array_key_exists('NewName', $table->getColumns()));
        $table->drop();
    }
    
    /***/
    public function test_addIndex() {
        $table = Table::create($this->db, 'T0001', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        $this->assertNotNull(Table::get($this->db, 'T0001'));
        $table->addIndex('ddd', array('name'));
        $table->drop();
    }
    
    /** */
    public function test_dropIndex() {
        $table = Table::create($this->db, 'T0001', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        $this->assertNotNull(Table::get($this->db, 'T0001'));
        $table->addIndex('ddd', array('name'));
        $table->dropIndex('ddd');
        $table->drop();
    }
    
    /***/
    public function test_addFk() {
        $table1 = Table::create($this->db, 'T0001', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        
        $table2 = Table::create($this->db, 'T0002', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        
        $table1->addForginKey('test', array('id'), 'T0002', array('id'));
        
        $table1->drop();
        $table2->drop();
    }
    
    /***/
    public function test_dropFk() {
        $table1 = Table::create($this->db, 'T0001', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        
        $table2 = Table::create($this->db, 'T0002', array(
            'id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "XXXXX"',
            'name VARCHAR(255)',
        ));
        
        $table1->addForginKey('test', array('id'), 'T0002', array('id'));
        $table1->dropForginKey('test');
        
        $table1->drop();
        $table2->drop();
    }
}