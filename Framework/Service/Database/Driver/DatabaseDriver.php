<?php
namespace X\Service\Database\Driver;
use X\Service\Database\Table\Column;

interface DatabaseDriver {
    /** option index for check preparing custom expression */
    const OPT_PREPARE_CUSTOM_EXPRESSION = 0;
    /** option for checking able to drop column */
    const OPT_ALTER_TABLE_DROP_COLUMN = 1;
    /** option for checking able to change column */
    const OPT_ALTER_TABLE_CHANGE_COLUMN = 2;
    /** optioni for check able to auto increase on insert */
    const OPT_AUTO_INCREASE_ON_INSERT = 3;
    /** option for checking wether able to rename column on changing column */
    const OPT_RENAME_COLUMN_ON_CHANGING_COLUMN = 4;
    /** option for checking wehter uppercase table name */
    const OPT_UPPERCASE_TABLE_NAME = 5;
    /** option for checking wether uppercase column name */
    const OPT_UPPERCASE_COLUMN_NAME = 6;
    /** option for checking wether able to rename table */
    const OPT_ALTER_TABLE_RENAME = 7;
    
    /**
     * @param unknown $type
     */
    function mapColumnTypeToDatabaseType( $type );
    
    /**
     * Driver must be configable.
     * @param array $config
     */
    function __construct( array $config=array() );
    
    /** @return string */
    function getName();
    
    /**
     * Execute the sql query and return the number of 
     * affected rows.
     * @param string $query the query to exec
     * @param array $params the key is param's name, and 
     * value is the value to bind
     * @return integer
     */
    function exec( $query, array $params=array() );
     
    /**
     * Execute the sql query and return the query result
     * @param string $query the query to exec
     * @param array $params the key is param's name, and 
     * value is the value to bind
     * @return \X\Service\Database\QueryResult
     */
    function query( $query, array $params=array() );
    
    /**
     * get the last insert id
     * @param string $sequenceName name of sequence
     * @return mixed
     */
    function getLastInsertId($sequenceName=null);
    
    /**
     * quote table name to make sure it's safe in query
     * @param string $tableName
     * @return string
     */
    function quoteTableName( $tableName );
    
    /**
     * quote column name to make sure it's safe in query
     * @param string $columnName
     * @return string
     */
    function quoteColumnName( $columnName, $options=array() );
    
    /**
     * @param unknown $value
     * @return string
     */
    function quoteValue($value);
    
    /**
     * get last error code
     * @return mixed
     */
    function getErrorCode();
    
    /**
     * get last error message
     * @return string
     */
    function getErrorMessage();
    
    /**
     * get table name list
     * @return \string[]
     */
    function tableList();
    
    /**
     * get column list of a table
     * @return Column[]
     */
    function columnList( $tableName );
    
    /**
     * @param unknown $name
     * @param unknown $default
     */
    function getOption( $name, $default=null );
    
    //function insert( $option );
    //function batchInsert( $option );
    //function delete( $option );
    
//     function update();
//     function select();
    
//     function tableCreate();
//     function tableDelete();
//     function tableRename();
//     function tableTruncate();
    
//     function columAdd();
//     function columnDelete();
//     function columnUpdate();
//     function columnRename();
    
//     function indexAdd();
//     function indexDelete();
    
//     function foreignKeyAdd();
//     function foreignKeyDelete();
    
//     function storedProcedureAdd();
//     function storedProcedureDelete();
//     function storedProcedureCall();
    
//     function functionAdd();
//     function functionDelete();
//     function functionCall();

//     function beginTransaction();
//     function commit();
//     function rollback();
}