<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\Database;

/**
 * 
 */
use X\Service\XDatabase\Core\Util\Exception;

/**
 * Database
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 * @method array query( $query ) Executes an SQL statement, returning an array as result set.
 * @method boolean exec( $query ) Executes an SQL statement, returning true or false.
 * @method integer lastInertId() Returns the ID of the last inserted row or sequence value.
 * @method string quoteColumnName($name)
 * @method string quoteTableName($name)
 * @method string quote($value)
 */
class Database {
    /**
     * The config about the db.
     * @var array
     */
    protected $config = array();
    
    /**
     * The driver that current database object is using.
     * @var \X\Database\Driver\Driver
    */
    protected $driver = null;
    
    /**
     * Initiate the database by given config information.
     * @param array $config The config information to initiate the database.
     */
    public function __construct( $config ) {
        $this->config = $config;
    }
    
    /**
     * @param unknown $name
     * @return multitype:
     */
    public function getConfig( $name ) {
        return $this->config[$name];
    }
    
    /**
     * Do magic call
     * @param string $name The name of method to call
     * @param array $parms The parmas for the method
     */
    public function __call( $name, $parms ) {
        return call_user_func_array(array($this->getDriver(), $name), $parms);
    }
    
    /**
     * Get driver for current Database object.
     * @return \X\Database\Driver\Driver
     */
    protected function getDriver() {
        if ( null === $this->driver ) {
            $this->driver = $this->getDriverByConfig();
        }
        return $this->driver;
    }
    
    /**
     * Get The current for current Database object by config.
     * @return \X\Database\Driver\Driver
     */
    protected function getDriverByConfig() {
        $driverName = null;
        $driverHandler = null;
        if ( isset($this->config['dsn']) ) {
            $information = explode(':', $this->config['dsn']);
            $driverName = ucfirst($information[0]);
            $driverHandler = 'PDO';
        } else {
            throw new Exception('Can not find driver from config.');
        }
        
        $driverClass = 'X\\Service\\XDatabase\\Core\\Driver\\'.$driverName.'\\'.$driverHandler;
        if ( !class_exists($driverClass) ) {
            throw new Exception('Unable to find database driver "'.$driverClass.'".');
        }
        
        return new $driverClass($this->config);
    }
}