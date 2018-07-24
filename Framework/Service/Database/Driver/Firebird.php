<?php
namespace X\Service\Database\Driver;
use X\Service\Database\Table\Column;
/**
 * @todo no truncate
 * @todo can not insert mutil rows
 * @todo table name and column name is upper case
 * @link https://scott.yang.id.au/2004/01/limit-in-select-statements-in-firebird.html
 */
class Firebird extends DatabaseDriverPDO {
    /** @var string host address of mssql server */
    protected $host = 'localhost';
    /** @var integer port to mssql server */
    protected $port = 3050;
    /** @var string username to mssql server */
    protected $username = null;
    /** @var string password to mssql service */
    protected $password = null;
    /** @var string database name to access */
    protected $dbname;
    /** @var string name of charset ot server */
    protected $charset="UTF-8";
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriverBase::init()
     */
    protected function init() {
        $dsn = "firebird:dbname={$this->host}/{$this->port}:{$this->dbname}";
        $this->connection = new \PDO($dsn,$this->username,$this->password);
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
    public function quoteColumnName($columnName) {
        return '"'.str_replace('"', '""', $columnName).'"';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::getName()
     */
    public function getName() {
        return 'firebird';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::tableList()
     * @link http://www.firebirdfaq.org/faq174/
     */
    public function tableList() {
        $query = 'SELECT RDB$RELATION_NAME AS TBNAME
            FROM RDB$RELATIONS
            WHERE RDB$VIEW_BLR IS NULL
            AND (RDB$SYSTEM_FLAG IS NULL OR RDB$SYSTEM_FLAG = 0)';
        $tables = $this->query($query)->fetchAll();
        foreach ( $tables as $index => $table ) {
            $tables[$index] = trim($table['TBNAME']);
        }
        return $tables;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Driver\DatabaseDriver::columnList()
     * @link https://stackoverflow.com/questions/12070162/how-can-i-get-the-table-description-fields-and-types-from-firebird-with-dbexpr
     * @link https://stackoverflow.com/questions/36946560/getting-all-tables-columns-and-their-datatype-firebird-in-one-query
     */
    public function columnList($tableName) {
        $query = 'SELECT
              R.RDB$FIELD_NAME AS NAME,
              R.RDB$FIELD_SOURCE AS SOURCE,
              F.RDB$FIELD_LENGTH AS LENGTH,
              CASE F.RDB$FIELD_TYPE
                 WHEN 7 THEN \'SMALLINT\'
                 WHEN 8 THEN \'INTEGER\'
                 WHEN 9 THEN \'QUAD\'
                 WHEN 10 THEN \'FLOAT\'
                 WHEN 11 THEN \'D_FLOAT\'
                 WHEN 12 THEN \'DATE\'
                 WHEN 13 THEN \'TIME\'     
                 WHEN 14 THEN \'CHAR\'
                 WHEN 16 THEN \'INT64\'
                 WHEN 27 THEN \'DOUBLE\'
                 WHEN 35 THEN \'TIMESTAMP\'
                 WHEN 37 THEN \'VARCHAR\'
                 WHEN 40 THEN \'CSTRING\'
                 WHEN 261 THEN \'BLOB\'
                 ELSE \'UNKNOWN\'
              END AS TYPE,
              F.RDB$FIELD_SCALE AS SCALE,
              F.RDB$FIELD_SUB_TYPE AS SUBTYPE
          FROM
              RDB$RELATION_FIELDS R
              JOIN RDB$FIELDS F
                ON F.RDB$FIELD_NAME = R.RDB$FIELD_SOURCE
              JOIN RDB$RELATIONS RL
                ON RL.RDB$RELATION_NAME = R.RDB$RELATION_NAME
          WHERE
              COALESCE(R.RDB$SYSTEM_FLAG, 0) = 0
              AND
              COALESCE(RL.RDB$SYSTEM_FLAG, 0) = 0
              AND
              RL.RDB$VIEW_BLR IS NULL
              AND R.RDB$RELATION_NAME = :tbname
          ORDER BY
              R.RDB$RELATION_NAME,
              R.RDB$FIELD_POSITION';
        $columns = $this->query($query, array(':tbname'=>$tableName))->fetchAll();
        
        $list = array();
        foreach ( $columns as $item ) {
            $name = trim($item['NAME']);
            $column = new Column();
            $column->setName($name);
            $column->setLength($item['LENGTH']);
            $column->setType($item['TYPE']);
            $list[$name] = $column;
        }
        
        return $list;
    }

}