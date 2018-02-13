<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\Table;
/**
 * Use statements
 */
use X\Core\X;
use X\Service\XDatabase\Core\Util\Exception;
use X\Service\XDatabase\Service as XDatabaseService;
use X\Service\XDatabase\Core\SQL\Builder as SQLBuilder;
use X\Service\XDatabase\Core\SQL\Func\Count as SQLFuncCount;
/**
 * Table manager to manage database tables.
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Manager {
    /**
     * Get table names in the database.
     * @return array
     */
    public static function getTables() {
        return self::getDatabase()->getTables();
    }
    
    /**
     * @param unknown $name
     * @return boolean
     */
    public static function hasTable( $name ) {
        try {
            $table = self::open($name);
            return true;
        } catch ( Exception $e ) {
            return false;
        }
    }
    
    /**
     * Create a new table.
     * @param string $name The name of table to create
     * @param array $columns The column definitions.
     * @param string $primaryKey
     * @return \X\Service\XDatabase\Core\Table\Manager 
     */
    public static function create( $name, $columns, $primaryKey=null ) {
        $sql = SQLBuilder::build()->createTable()
            ->name($name)
            ->columns($columns)
            ->primaryKey($primaryKey)
            ->toString();
        
        self::executeSQLQueryWithOutResult($sql);
        return new Manager($name);
    }
    
    /**
     * Get a new table object by name.
     * @param string $name The name of table to open for operation.
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public static function open( $name ) {
        $manager = new Manager($name);
        return $manager;
    }
    
    /**
     * Get the information about table.
     * @return array
     */
    public function getInformation() {
        $sql = SQLBuilder::build()->describe()->name($this->name)->toString();
        $result = $this->query($sql);
        return $result;
    }
    
    /**
     * Drop the operating table.
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function drop() {
        $sql = SQLBuilder::build()->dropTable()
            ->name($this->name)
            ->toString();
        self::executeSQLQueryWithOutResult($sql);
        return $this;
    }
    
    /**
     * Truncate the operating table.
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function truncate() {
        $query = SQLBuilder::build()->truncate()
            ->name($this->name)
            ->toString();
        self::executeSQLQueryWithOutResult($query);
        return $this;
    }
    
    /**
     * Insert a new record into the operating table.
     * @param array $values The value of new record.
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function insert( $values ) {
        $query = SQLBuilder::build()->insert()
            ->into($this->name)->values($values)->toString();
        self::executeSQLQueryWithOutResult($query);
        return $this;
    }
    
    /**
     * @param unknown $newValue
     * @param unknown $condition
     */
    public function update ( $newValue, $condition=null,$limit=0, $offset=0) {
        $query = SQLBuilder::build()->update()
            ->values($newValue)
            ->table($this->name)
            ->limit($limit)
            ->offset($offset)
            ->where($condition)
            ->toString();
        self::executeSQLQueryWithOutResult($query);
        return $this;
    }
    
    /**
     * @return number
     */
    public function countRows() {
        $query = SQLBuilder::build()->select()->expression(new SQLFuncCount(), 'row_count')->from($this->name)->toString();
        $result = self::query($query);
        return $result[0]['row_count'];
    }
    
    /**
     * Rename the operating table.
     * @param string $name The new name for operating table.
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function rename( $name ) {
        $query = SQLBuilder::build()->rename()
            ->name($this->name)
            ->newName($name)
            ->toString();
        self::executeSQLQueryWithOutResult($query);
        $this->name = $name;
        return $this;
    }
    
    /**
     * Add new column to the operating table.
     * @param string $name
     * @param array $definition
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function addColumn( $name, $definition ) {
        return $this->doAlterAction('addColumn', array($name, $definition));
    }
    
    /**
     * Drop column from the operating table.
     * @param string $name The name of column to drop
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function dropColumn( $name ){
        return $this->doAlterAction('dropColumn', array($name));
    }
    
    /**
     * Change column from the operating table.
     * @param string $name
     * @param string $definition
     * @param string $newName
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function changeColumn($name, $definition, $newName=null){
        $newName = (null===$newName) ? $name : $newName;
        return $this->doAlterAction('changeColumn', array($name, $newName, $definition));
    }
    
    /**
     * Add index for the operating table.
     * @param string $name The name of index to add.
     * @param array $columns The column name list that index contains
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function addIndex( $name, $columns ) {
        return $this->doAlterAction('addIndex', array($name, $columns));
    }
    
    /**
     * Drop index from the operating table.
     * @param string $name The name index to drop
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function dropIndex($name){
        return $this->doAlterAction('dropIndex', array($name));
    }
    
    /**
     * Add primary key for the operating table.
     * @param string $columns The name of column to set as primary key
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function addPrimaryKey($columns){
        return $this->doAlterAction('addPrimaryKey', array($columns));
    }
    
    /**
     * Drop primary key for the operating table.
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function dropPrimaryKey(){
        return $this->doAlterAction('dropPrimaryKey');
    }
    
    /**
     * Add unique column for the operating table.
     * @param string $columns The name of column to set as unique.
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function addUnique( $columns ){
        return $this->doAlterAction('addUnique', array($columns));
    }
    
    /**
     * Do alter table action by given action name and parms.
     * @param string $action The action name for alter tabel.
     * @param array $parms The parms to that action
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    private function doAlterAction( $action, $parms=array() ) {
        $builder = SQLBuilder::build()->alterTable()->name($this->name);
        $builder = call_user_func_array(array($builder, $action), $parms);
        self::executeSQLQueryWithOutResult($builder->toString());
        return $this;
    }
    
    /**
     * @param string $type
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function lock( $type ) {
        $query = SQLBuilder::build()->lock()->setType($type)->name($this->name);
        $query = $query->toString();
        self::executeSQLQueryWithOutResult($query);
        return $this;
    }
    
    /**
     * @return \X\Service\XDatabase\Core\Table\Manager
     */
    public function unlock() {
        self::executeSQLQueryWithOutResult(SQLBuilder::build()->unlock()->toString());
        return $this;
    }
    
    const LOCK_READ = 'READ';
    const LOCK_WRITE = 'WRITE';
    
    /**
     * The name of the target table.
     * @var string
     */
    private $name = null;
    
    /**
     * Initiate the object by given table name
     * @param string $name The name of table to operate.
     */
    private function __construct( $name ) {
        $this->name = $name;
    }
    
    /**
     * Execute a query an return the result.
     * @param string $sql
     * @throws Exception
     * @return unknown
     */
    private function query( $sql ) {
        $result = self::getDatabase()->query($sql);
        return $result;
    }
    
    /**
     * Get the xdatabse service.
     * @return \X\Service\XDatabase\Core\Database\Database
     */
    private static function getDatabase() {
        $service = X::system()->getServiceManager()->get(XDatabaseService::getServiceName());
        return $service->get();
    }
    
    /**
     * executeSQLQueryWithOutResult
     * @param string $query The query to execute
     * @return void
     */
    private static function executeSQLQueryWithOutResult( $query ) {
        self::getDatabase()->exec($query);
    }
}