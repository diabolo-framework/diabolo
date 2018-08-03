<?php
namespace X\Service\Database\Query\Oracle;
use X\Service\Database\Query\Select as QuerySelect;
/**
 * @author Michael Luthor <michaelluthor@163.com>
 */
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
        if ( null === $this->offset ) {
            return;
        }
        $query[] = "OFFSET {$this->offset} ROWS";
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildLimit( &$query ) {
        if ( null === $this->limit ) {
            return;
        }
        $query[] = "FETCH NEXT {$this->limit} ROWS ONLY";
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildFrom( &$query ) {
        if ( empty($this->tables) ) {
            $this->from('DUAL');
        }
        parent::buildFrom($query);
    }
}