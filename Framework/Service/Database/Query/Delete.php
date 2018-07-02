<?php
namespace X\Service\Database\Query;
use X\Service\Database\DatabaseException;
class Delete extends DatabaseLimitableQuery {
    /** @var string */
    private $table = null;
    
    /**
     * @param string $table
     * @return self
     */
    public function table( $table ) {
        $this->table = $table;
        return $this;
    }
    
    /**
     * @param string $table
     * @return self
     */
    public function from($table) {
        return $this->table($table);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'DELETE';
        $this->buildTable($query);
        $this->buildCondition($query);
        $this->buildOrderBy($query);
        $this->buildLimit($query);
        return implode(' ', $query);
    }
    
    /** @param array $query */
    private function buildTable( &$query ) {
        if ( null === $this->table ) {
            throw new DatabaseException('no table specified on delete query');
        }
        
        $table = $this->getDatabase()->quoteTableName($this->table);
        $query[] = "FROM {$table}";
    }
    
    /**
     * @return integer
     */
    public function exec() {
        return $this->getDatabase()->exec($this->toString(), $this->queryParams);
    }
}