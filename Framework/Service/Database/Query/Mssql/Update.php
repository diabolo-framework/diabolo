<?php
namespace X\Service\Database\Query\Mssql;
use X\Service\Database\Query\Update as QueryUpdate;
class Update extends QueryUpdate {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'UPDATE';
        $this->buildLimit($query);
        $this->buildTable($query);
        $this->buildValues($query);
        $this->buildCondition($query);
        $this->buildOrderBy($query);
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
        $query[] = 'TOP ('.$this->limit.')';
    }
}