<?php
namespace X\Service\Database\Driver;
use OAuth2\Storage\Pdo;
use X\Service\Database\Table\Column;

class Postgresql extends DatabaseDriverPDO {
    /** @var string host address of mysql server */
    protected $host;
    /** @var string username to mysql server */
    protected $username;
    /** @var string password to mysql service */
    protected $password;
    /** @var string database name to access */
    protected $dbname;
    /** @var integer port to mysql server */
    protected $port = 5432;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverPDO::mapColumnTypeToDatabaseType()
     */
    public function mapColumnTypeToDatabaseType( $type ) {
        $map = array(
            'STRING' => 'VARCHAR',
            'INTEGER' => 'INT8',
            'DECIMAL' => 'DECIMAL',
        );
        $type = strtoupper($type);
        return isset($map[$type]) ? $map[$type] : $type;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverPDO::init()
     */
    protected function init() {
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
        $this->connection = new \PDO($dsn, $this->username, $this->password);
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
     * @see \X\Service\Database\Driver\DatabaseDriver::getName()
     */
    public function getName() {
        return 'postgresql';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::tableList()
     */
    public function tableList() {
        $query = 'SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname NOT IN(\'pg_catalog\', \'information_schema\')';
        $tables = $this->query($query)->fetchAll();
        foreach ( $tables as $index => $table ) {
            $tables[$index] = $table['tablename'];
        }
        return $tables;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::columnList()
     */
    public function columnList($tableName) {
        $query = 'SELECT * FROM information_schema.columns WHERE table_name=:table AND table_catalog=:catalog';
        $columns = $this->query($query, array(':table'=>$tableName,'catalog'=>$this->dbname))->fetchAll();
        
        $query = '
            SELECT K.TABLE_NAME,
            K.COLUMN_NAME,
            K.CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS C
            JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS K
            ON C.TABLE_NAME = K.TABLE_NAME
            AND C.CONSTRAINT_CATALOG = K.CONSTRAINT_CATALOG
            AND C.CONSTRAINT_SCHEMA = K.CONSTRAINT_SCHEMA
            AND C.CONSTRAINT_NAME = K.CONSTRAINT_NAME
            WHERE C.CONSTRAINT_TYPE = \'PRIMARY KEY\' 
            AND C.TABLE_NAME=:table';
        $primaryKey = $this->query($query,array(':table'=>$tableName))->fetchAll();
        $primaryKey = isset($primaryKey[0]) ? $primaryKey[0]['column_name'] : null;
        
        $quotedAsValueTableName = $this->quoteValue($tableName);
        $list = array();
        foreach ( $columns as $item ) {
            $column = new Column();
            $column->setName($item['column_name']);
            $column->setType($item['data_type']);
            $column->setIsNotNull( "NO" === $item['is_nullable'] );
            $column->setDefaultValue($item['column_default']);
            $column->setIsAutoIncrement(false !== strpos($item['column_default'], 'nextval'));
            $column->setIsPrimary($primaryKey === $item['column_name']);
            
            $query = "
            SELECT c.conname, pg_get_constraintdef(c.oid)
            FROM   pg_constraint c
            JOIN  (
              SELECT array_agg(attnum::int) AS attkey
              FROM   pg_attribute
              WHERE  attrelid = {$quotedAsValueTableName}::regclass
              AND    attname  = ANY('{{$item['column_name']}}')
            ) a ON c.conkey::int[] <@ a.attkey AND c.conkey::int[] @> a.attkey
            WHERE  c.contype  = 'u'
            AND    c.conrelid = {$quotedAsValueTableName}::regclass";
            $isUniqueKey = $this->query($query)->count() > 0;
            $column->setIsUnique($isUniqueKey);
            
            $list[$column->getName()] = $column;
        }
        return $list;
    }

}