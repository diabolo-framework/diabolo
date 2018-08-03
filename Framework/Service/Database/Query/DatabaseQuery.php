<?php
namespace X\Service\Database\Query;
use X\Service\Database\Database;
use X\Service\Database\Driver\DatabaseDriver;
abstract class DatabaseQuery {
    /** @var Database */
    private $db = null;
    /** @var array */
    protected $queryParams = array();
    
    /**
     * @param array $params
     * @return \X\Service\Database\Query\Condition
     */
    public function setPreviousParams( &$params ) {
        $this->queryParams = &$params;
        return $this;
    }
    
    /**
     * @param mixed $value
     * @return string
     */
    protected function getParamKey( $value, $expr=null ) {
        $prepareCustomExpr = $this->db->getDriver()->getOption(DatabaseDriver::OPT_PREPARE_CUSTOM_EXPRESSION, true);
        if ( null!==$expr && !is_string($expr) && !$prepareCustomExpr ) {
            return is_string($value) ? $this->db->quoteValue($value) : $value;
        }
    
        if ( $value instanceof Expression ) {
            return $value->toString();
        } else if ( $value instanceof DatabaseQuery ) {
            return $value->toString();
        }
    
        $paramsKey = ':qp'.count($this->queryParams);
        $this->queryParams[$paramsKey] = $value;
        return $paramsKey;
    }
    
    /**
     * @param Database $db
     */
    public function __construct( Database $db ) {
        $this->db = $db;
    }
    
    /** @return self */
    public function setDatabase( $db ) {
        $this->db = $db;
        return $this;
    }
    
    /** @return \X\Service\Database\Database */
    protected function getDatabase() {
        return $this->db;
    }
    
    /**
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }
    
    /**
     * @return string
     */
    abstract public function toString();
}