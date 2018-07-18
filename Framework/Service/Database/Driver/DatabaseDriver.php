<?php
namespace X\Service\Database\Driver;
use X\Service\Database\Table\Column;

interface DatabaseDriver {
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
    function quoteColumnName( $columnName );
    
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