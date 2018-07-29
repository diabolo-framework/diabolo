<?php
namespace X\Service\Database\Query;
use X\Service\Database\Database;
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