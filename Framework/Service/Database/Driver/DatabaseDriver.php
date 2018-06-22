<?php
namespace X\Service\Database\Driver;
interface DatabaseDriver {
    /**
     * Driver must be configable.
     * @param array $config
     */
    function __construct( array $config=array() );
    
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
     * get last error code
     * @return mixed
     */
    function getErrorCode();
    
    /**
     * get last error message
     * @return string
     */
    function getErrorMessage();
    
    //function insert( $option );
    //function batchInsert( $option );
    //function delete( $option );
    
//     function update();
//     function select();
    
//     function tableCreate();
//     function tableDelete();
//     function tableRename();
//     function tableList();
//     function tableTruncate();
    
//     function columAdd();
//     function columnDelete();
//     function columnUpdate();
//     function columnList();
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