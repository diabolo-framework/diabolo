<?php
namespace X\Service\Database\Query\Oracle;
use X\Service\Database\Query\Delete as QueryDelete;
use X\Service\Database\Query;
use X\Service\Database\Query\Expression;
class Delete extends QueryDelete {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'DELETE';
        $this->buildTable($query);
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
        $query[] = 'WHERE ROWID IN (';
        $query[] = Query::select($this->getDatabase())
            ->from($this->table)
            ->column(new Expression('ROWID'))
            ->limit($this->limit)
            ->orders($this->orders)
            ->toString();
        $query[] = ')';
    }
}