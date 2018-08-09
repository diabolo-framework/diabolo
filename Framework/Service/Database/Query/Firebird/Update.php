<?php
namespace X\Service\Database\Query\Firebird;
use X\Service\Database\Query\Update as QueryUpdate;
class Update extends QueryUpdate {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'UPDATE';
        $this->buildTable($query);
        $this->buildValues($query);
        $this->buildCondition($query);
        $this->buildOrderBy($query);
        $this->buildLimit($query);
        return implode(' ', $query);
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildLimit( &$query ) {
        if ( null === $this->limit ) {
            return;
        }
        $query[] = 'ROWS 1 TO '.$this->limit;
    }
}