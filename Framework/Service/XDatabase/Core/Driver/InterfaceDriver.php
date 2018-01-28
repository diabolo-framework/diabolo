<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\Driver;

/**
 * Driver
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
interface InterfaceDriver {
    /**
     * Execute the query, and return true on successed and false if failed.
     * @param string $query The query to execute.
     * @return boolean
     */
    public function exec( $query );
    
    /**
     * Execute the query and return the result of query on successed 
     * and false if failed.
     * @param string $query
     * @return boolean|array
     */
    public function query( $query );
    
    /**
     * Quote the string for safety using in query.
     * @param string $string The value to quote.
     * @return string
     */
    public function quote( $string );
    
    /**
     * Quote the name of table for safty using in query string.
     * @param string $name The name to quoted.
     * @return string
     */
    public function quoteTableName( $name );
    
    /**
     * quote the name of column
     * @param string $name
     * @return string
     */
    public function quoteColumnName( $name );
    
    /**
     * Get the last insert id after execute a insert query.
     * @return integer
     */
    public function getLastInsertId();
    
    /**
     * Get the list of table names
     * @return array
     */
    public function getTables();
}