<?php
namespace X\Service\Database;
class QueryResult {
    /** @var \PDOStatement */
    private $result = null;
    
    /**
     * @param \PDOStatement $result
     */
    public function __construct( \PDOStatement $result ) {
        $this->result = $result;
    }
    
    /**
     * @param string $target class name 
     * @return array
     */
    public function fetchAll( $target=null ) {
        return $this->result->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * @param string $target class name
     * @return array
     */
    public function fetch( $target=null ) {
        return $this->result->fetch(\PDO::FETCH_ASSOC);
    }
}