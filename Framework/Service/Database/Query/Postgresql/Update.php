<?php
namespace X\Service\Database\Query\Postgresql;
use X\Service\Database\DatabaseException;
use X\Service\Database\Query;
class Update extends \X\Service\Database\Query\Update {
    /** @var string */
    private $primaryKeyName = null;
    
    /**
     * @param string $name
     * @return self
     */
    public function setPrimaryKeyName( $name ) {
        $this->primaryKeyName = $name;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'UPDATE';
        $this->buildTable($query);
        $this->buildValues($query);
        if ( null !== $this->limit || !empty($this->orders) ) {
            $this->buildConditionAsSubQuery($query);
        } else {
            $this->buildCondition($query);
        }
        return implode(' ', $query);
    }
    
    /**
     * @param unknown $query
     */
    private function buildConditionAsSubQuery( &$query ) {
        if ( null === $this->primaryKeyName ) {
            throw new DatabaseException('primary key name must be specified');
        }
        $query[] = 'WHERE '.$this->getDatabase()->quoteColumnName($this->primaryKeyName).' IN (';
        $query[] = Query::select($this->getDatabase())
            ->from($this->table)
            ->column($this->primaryKeyName)
            ->limit($this->limit)
            ->orders($this->orders)
            ->toString();
        $query[] = ')';
    }
}