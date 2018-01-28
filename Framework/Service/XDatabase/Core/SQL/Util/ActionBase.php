<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Util;
/**
 * Use statements
 */
use X\Core\X;
use X\Service\XDatabase\Service as XDatabaseService;
/**
 * action base class
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
abstract class ActionBase {
    /**
     * This value contains each part of query string.
     * @var array
     */
    protected $sqlCommand = array();
    
    /**
     * Get the handlers array, each element is the method name
     * of the subclass class 
     * @return string[]
     */
    abstract protected function getBuildHandlers();
    
    /**
     * Convert this action into sql command string.
     * @return string
     */
    public function toString() {
        $this->sqlCommand = array();
        foreach ( $this->getBuildHandlers() as $handler ) {
            $handlerMethod = 'buildHandler'.ucfirst($handler);
            if ( method_exists($this, $handlerMethod) ) {
                call_user_func_array(array($this, $handlerMethod), array());
            } else {
                $this->sqlCommand[] = $handler;
            }
        }
        
        return implode(' ', $this->sqlCommand);
    }
    
    /**
     * Quote the column's name for safty use in query string.
     * @param string $name The name of column to quote.
     * @return string
     */
    protected function quoteColumnName( $name ) {
        $database = $this->getDatabase();
        
        $column = explode('.', $name);
        $column = array_map(array($database, 'quoteColumnName'), $column);
        $column = implode('.', $column);
        return $column;
    }
    
    /**
     * Quote the columns' name for safty use in query string.
     * @param array $names The column name list to quote.
     * @return array
     */
    protected function quoteColumnNames( $names ) {
        return array_map(array($this, 'quoteColumnName'), $names);
    }
    
    /**
     * Quote the table name for safty use in query string.
     * @param string $name The name of table to quote.
     * @return string
     */
    protected function quoteTableName( $name ) {
        return $this->getDatabase()->quoteTableName($name);
    }
    
    /**
     * Quote the string value
     * @param string $value
     * @return string
     */
    protected function quoteValue( $value ) {
        return $this->getDatabase()->quote($value);
    }
    
    /**
     * get current database from service.
     * @return \X\Service\XDatabase\Core\Database
     */
    protected function getDatabase() {
        /* @var $service XDatabaseService */
        $service = X::system()->getServiceManager()->get(XDatabaseService::getServiceName());
        return $service->get();
    }
}