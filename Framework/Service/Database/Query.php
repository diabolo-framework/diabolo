<?php
namespace X\Service\Database;
use X\Service\Database\Query\Select;
use X\Service\Database\Query\Insert;
use X\Service\Database\Query\Delete;
use X\Service\Database\Query\Update;
use X\Service\Database\Query\CreateTable;
use X\Service\Database\Query\DropTable;
use X\Service\Database\Query\TruncateTable;
use X\Service\Database\Query\AlterTable;
use X\Service\Database\Query\DatabaseQuery;
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
     * @return \X\Service\Database\Query\Delete|\X\Service\Database\Query\Postgresql\Delete
     */
    public static function delete( $db ) {
        if ( is_string($db) ) {
            $db = Service::getService()->getDB($db);
        }
        $name = $db->getDriver()->getName();
        $class = '\\X\\Service\\Database\\Query\\'.ucfirst($name).'\\Delete';
        return class_exists($class) ? new $class($db) : new Delete($db);
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
    
    /**
     * @param string|Database $db
     * @return \X\Service\Database\Query\CreateTable
     */
    public static function createTable( $db ) {
        $db = Service::getService()->getDB($db);
        return new CreateTable($db);
    }
    
    /**
     * @param string|Database $db
     * @return \X\Service\Database\Query\DropTable
     */
    public static function dropTable( $db ) {
        $db = Service::getService()->getDB($db);
        return new DropTable($db);
    }
    
    /**
     * @param string|Database $db
     * @return \X\Service\Database\Query\TruncateTable
     */
    public static function truncateTable( $db ) {
        return self::getQuery($db, 'TruncateTable');
    }
    
    /**
     * @param string|Database $db
     * @return \X\Service\Database\Query\AlterTable
     */
    public static function alterTable( $db ) {
        return self::getQuery($db, 'AlterTable');
    }
    
    /**
     * @param string|Database $db
     * @param string $queryName
     * @return DatabaseQuery
     */
    private static function getQuery( $db, $queryName ) {
        $db = Service::getService()->getDB($db);
        
        $driverName = $db->getDriver()->getName();
        $defaultQueryClass = '\\X\\Service\\Database\\Query\\'.$queryName;
        $driverQueryClass = '\\X\\Service\\Database\\Query\\'.ucfirst($driverName).'\\'.$queryName;
        return class_exists($driverQueryClass) ? new $driverQueryClass($db) : new $defaultQueryClass($db);
    }
}