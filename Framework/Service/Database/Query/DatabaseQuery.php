<?php
namespace X\Service\Database\Query;
use X\Service\Database\Database;
abstract class DatabaseQuery {
    /** @var Database */
    private $db = null;
    
    /**
     * @param Database $db
     */
    public function __construct( Database $db ) {
        $this->db = $db;
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