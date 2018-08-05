<?php
namespace X\Service\Database\Query\Mssql;
use X\Service\Database\Query\Delete as QueryDelete;
use X\Service\Database\DatabaseException;
use X\Service\Database\Query\Condition;
use X\Service\Database\Query;
class Delete extends QueryDelete {
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
        $query[] = 'DELETE';
        if ( empty($this->orders) && null!==$this->limit ) {
            $this->buildLimit($query);
        }
        $this->buildTable($query);
        if ( !empty($this->orders) ) {
            $this->buildOrderIntoCondition($query);
        } else {
            $this->buildCondition($query);
        }
        return implode(' ', $query);
    }
    
    /**
     * @param unknown $query
     */
    private function buildOrderIntoCondition( &$query ) {
        if ( null === $this->primaryKeyName ) {
            throw new DatabaseException('primary key name must be specified');
        }
        
        $condition = $this->condition;
        if ( !($condition instanceof Condition ) ) {
            $condition = Condition::build()->add($this->condition);
        }
        $condition->setPreviousParams($this->queryParams);
        $condition->setDatabase($this->getDatabase());
        $condition->in($this->primaryKeyName, Query::select($this->getDatabase())
            ->from($this->table)
            ->column($this->primaryKeyName)
            ->limit($this->limit)
            ->orders($this->orders));
        $query[] = 'WHERE '.$condition->toString();
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildLimit( &$query ) {
        if ( null === $this->limit ) {
            return;
        }
        $query[] = sprintf('TOP ( %d )', $this->limit);
    }
}