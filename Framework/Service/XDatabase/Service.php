<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase;
/**
 * 
 */
use X\Core\X;
use X\Service\XDatabase\Core\Database\Database;
use X\Service\XDatabase\Core\Util\Exception;
/**
 * X-database service
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Service extends \X\Core\Service\XService {
    /**
     * Name of x-database service.
     * @var string
     */
    protected static $serviceName = 'XDatabase';
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Service\XService::getPrettyName()
     */
    public function getPrettyName() {
        return '数据库管理服务';
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Service\XService::getDescription()
     */
    public function getDescription() {
        return '管理系统中所使用的数据库， 实现Active Record以及表, 迁移文件管理。';
    }
    
    /**
     * this value contains all database instances.
     * @var array
     */
    private $databases = array();
    
    /**
     * name of default database name.
     * @var string
     */
    const DEFAULT_DATADASE_NAME = 'default';
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Component\Manager::start()
     */
    public function start() {
        parent::start();
        
        $definedDatabases = $this->getConfiguration()->get('databases', array());
        foreach ( $definedDatabases as $name => $config ) {
            $this->load($name, $config);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Component\Manager::stop()
     */
    public function stop() {
        $this->databases = array();
        $this->currentDatabaseName = null;
        parent::stop();
    }
    
    /**
     * load database to service by given configuration.
     * @param string $name
     * @param mixed $config
     */
    public function load( $name, $config ) {
        $this->dbNotExistsRequired($name);
        $this->databases[$name] = new Database($config);
    }
    
    /**
     *  @return multitype:
     */
    public function getList() {
        $databases = $this->getConfiguration()->get('databases', array());
        return array_keys($databases);
    }
    
    /**
     * register new database to service.
     * @param string $name
     * @param mixed $config
     */
    public function register( $name, $config ) {
        $this->load($name, $config);
    
        $configuration = $this->getConfiguration();
        if ( !isset($configuration['databases']) ) {
            $configuration['databases'] = array();
        }
        $configuration['databases'][$name]=$config;
        $configuration->save();
    }
    
    /**
     * delete registered database information from service.
     * @param string $name
     */
    public function unregister( $name ) {
        $this->dbExistsRequired($name);
        unset($this->databases[$name]);
        if ( $this->currentDatabaseName === $name ) {
            $this->currentDatabaseName = null;
        }
    
        $configuration = $this->getConfiguration();
        unset($configuration['databases'][$name]);
        $configuration->save();
    }
    
    /**
     * check if database exists.
     * @param string $name
     * @return boolean
     */
    public function has( $name ) {
        return isset($this->databases[$name]);
    }
    
    /**
     * name of current actived database name.
     * @var string
     */
    private $currentDatabaseName = null;
    
    /**
     * get database instance by given name.
     * @param string $name
     * @return \X\Service\XDatabase\Core\Database\Database
     */
    public function get( $name=null ) {
        if ( null === $name ) {
            $name = $this->currentDatabaseName;
        }
        $this->dbExistsRequired($name);
        return $this->databases[$name];
    }
    
    /**
     * get current database name.
     * @return string
     */
    public function getCurrentName() {
        return $this->currentDatabaseName;
    }
    
    /**
     * switch active database to given name.
     * @param string $name
     */
    public function switchTo( $name ) {
        if ( null !== $name ) {
            $this->dbExistsRequired($name);
        }
        $this->currentDatabaseName = $name;
    }
    
    /**
     * check if db exists, and throw exception if it does.
     * @param string $name
     * @throws Exception
     */
    private function dbNotExistsRequired($name) {
        if ( $this->has($name) ) {
            throw new Exception('Database "'.$name.'" already exists.');
        }
    }
    
    /**
     * check if db exists, and throw exception if it does not.
     * @param string $name
     * @throws Exception
     */
    private function dbExistsRequired( $name ) {
        if ( !$this->has($name) ) {
            throw new Exception('Database "'.$name.'" does not exists.');
        }
    }
}