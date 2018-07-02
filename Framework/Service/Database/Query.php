<?php
namespace X\Service\Database;
use X\Service\Database\Query\Select;
use X\Service\Database\Query\Insert;
use X\Service\Database\Query\Delete;
use X\Service\Database\Query\Update;
class Query {
    /**
     * @param string|Database $db
     * @return \X\Service\Database\Query\Select
     */
    public static function select( $db ) {
        if ( is_string($db) ) {
            $db = Service::getService()->getDB($db);
        }
        return new Select($db);
    }
    
    /**
     * @param string|Database $db
     * @return \X\Service\Database\Query\Insert
     */
    public static function insert( $db ) {
        if ( is_string($db) ) {
            $db = Service::getService()->getDB($db);
        }
        return new Insert($db);
    }
    
    /**
     * @param string|Database $db
     * @return \X\Service\Database\Query\Delete
     */
    public static function delete( $db ) {
        if ( is_string($db) ) {
            $db = Service::getService()->getDB($db);
        }
        return new Delete($db);
    }
    
    /**
     * @param string|Database $db
     * @return \X\Service\Database\Query\Update
     */
    public static function update( $db ) {
        if ( is_string($db) ) {
            $db = Service::getService()->getDB($db);
        }
        return new Update($db);
    }
}