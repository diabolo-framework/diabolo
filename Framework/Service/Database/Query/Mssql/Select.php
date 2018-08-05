<?php
namespace X\Service\Database\Query\Mssql;
use X\Service\Database\Query\Select as QuerySelect;
class Select extends QuerySelect {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'SELECT';
        $this->buildExpressions($query);
        $this->buildFrom($query);
        $this->buildJoin($query);
        $this->buildCondition($query);
        $this->buildGroupBy($query);
        $this->buildHaving($query);
        $this->buildOrderBy($query);
        $this->buildOffset($query);
        $this->buildLimit($query);
        return implode(' ', $query);
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildOffset( &$query ) {
        if ( null === $this->offset && null===$this->limit ) {
            return;
        }
        
        if ( empty($this->orders) ) {
            $query[] = 'ORDER BY ( SELECT NULL )';
        }
        if ( null === $this->offset ) {
            $this->offset = 0;
        }
        $query[] = 'OFFSET '.$this->offset.' ROWS';
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildLimit( &$query ) {
        if ( null === $this->limit ) {
            return;
        }
        $query[] = 'FETCH NEXT '.$this->limit.' ROWS ONLY';
    }
}