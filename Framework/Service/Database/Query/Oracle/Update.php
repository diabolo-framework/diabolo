<?php
namespace X\Service\Database\Query\Oracle;
use X\Service\Database\Query\Condition;
use X\Service\Database\Query\Expression;
class Update extends \X\Service\Database\Query\Update {
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
        return implode(' ', $query);
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildCondition( &$query ) {
        if ( null === $this->limit && null === $this->condition ) {
            return;
        }
    
        $condition = Condition::build();
        if ( null !== $this->condition ) {
            $condition->add($this->condition);
        }
        if ( null !== $this->limit ) {
            $condition->lessOrEqual(new Expression('ROWNUM'), $this->limit);
        }
        $condition->setPreviousParams($this->queryParams);
        $condition->setDatabase($this->getDatabase());
        $query[] = 'WHERE '.$condition->toString();
    }
}