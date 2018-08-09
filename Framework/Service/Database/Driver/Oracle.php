<?php
namespace X\Service\Database\Driver;
use X\Service\Database\DatabaseException;
use X\Service\Database\Table\Column;
/**
 * @notice 值必须要使用单引号括起来
 * @notice 不支持lastInsertId， 需要使用seq获取唯一值
 * @notice 不支持多行插入，需要手工实现
 * @notice 不支持limit / offset 
 */
class Oracle extends DatabaseDriverPDO {
    /** @var string host address of oracle server */
    protected $host;
    /** @var string username to oracle server */
    protected $username;
    /** @var string password to oracle service */
    protected $password;
    /** @var string serviceName name to access */
    protected $serviceName;
    /** @var integer port to oracle server */
    protected $port = 1521;
    /** @var string chatset name */
    protected $charset = 'UTF8';
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverPDO::getOptions()
     */
    protected function getOptions() {
        return array(
            self::OPT_AUTO_INCREASE_ON_INSERT => false,
            self::OPT_RENAME_COLUMN_ON_CHANGING_COLUMN => false,
        );
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverPDO::mapColumnTypeToDatabaseType()
     */
    public function mapColumnTypeToDatabaseType( $type ) {
        $map = array(
            'STRING' => 'VARCHAR',
            'INTEGER' => 'INT',
            'DECIMAL' => 'DECIMAL',
        );
        $type = strtoupper($type);
        return isset($map[$type]) ? $map[$type] : $type;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverBase::init()
     */
    protected function init() {
        $tns = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST={$this->host})(PORT={$this->port})))".
                "(CONNECT_DATA=(SERVICE_NAME={$this->serviceName})))";
        $this->connection = new \PDO('oci:dbname='.$tns,$this->username,$this->password);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::quoteTableName()
     */
    public function quoteTableName($tableName) {
        return '"'.str_replace('"', '""', $tableName).'"';
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::quoteColumnName()
     */
    public function quoteColumnName($columnName, $options=array()) {
        return '"'.str_replace('"', '""', $columnName).'"';
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::getLastInsertId()
     */
    public function getLastInsertId($sequenceName=null) {
        throw new DatabaseException('database driver does not suport this function');
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::getName()
     */
    public function getName() {
        return 'oracle';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::tableList()
     */
    public function tableList() {
        $query = 'SELECT "TABLE_NAME" FROM "USER_TABLES"';
        $tables = $this->query($query)->fetchAll();
        foreach ( $tables as $index => $table ) {
            $tables[$index] = $table['TABLE_NAME'];
        }
        return $tables;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::columnList()
     */
    public function columnList($tableName) {
        $query = 'SELECT * FROM ALL_TAB_COLUMNS WHERE TABLE_NAME= :tableName';
        $columns = $this->query($query,array(':tableName'=>$tableName))->fetchAll();
        
        $pkQuery = 'SELECT cols.column_name
            FROM all_constraints cons, all_cons_columns cols 
            WHERE cons.constraint_type = \'P\' 
            AND cols.table_name=:tableName
            AND cons.constraint_name = cols.constraint_name 
            AND cons.owner = cols.owner 
            ORDER BY cols.table_name,cols.position';
        $pkName = $this->query($pkQuery,array(':tableName'=>$tableName))->fetchAll();
        $pkName = isset($pkName[0]) ? $pkName[0]['COLUMN_NAME'] : null;
        
        $list = array();
        foreach ( $columns as $item ) {
            $column = new Column();
            $column->setIsAutoIncrement(false);
            $column->setName($item['COLUMN_NAME']);
            $column->setType($item['DATA_TYPE']);
            $column->setLength($item['DATA_LENGTH']);
            $column->setIsNotNull( 'N' === $item['NULLABLE'] );
            $column->setDefaultValue($item['DATA_DEFAULT']);
            $column->setIsPrimary( $pkName === $item['COLUMN_NAME'] );
            
            $isUnique = $this->query('SELECT COLUMN_NAME FROM USER_IND_COLUMNS WHERE TABLE_NAME=:tableName AND COLUMN_NAME=:col',array(
                ':tableName' => $tableName,
                ':col' => $item['COLUMN_NAME']
            ))->count() > 0;
            $column->setIsUnique($isUnique);
            
            $list[$column->getName()] = $column;
        }
        return $list;
    }
}