<?php
namespace X\Service\Database\Query\Firebird;
use X\Service\Database\Query\Select as QuerySelect;
/**
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Select extends QuerySelect {
    /**
     * @param mixed $expression
     * @param string $alias
     * @return self
     */
    public function expression( $expression, $alias=null ) {
        if ( null === $alias && is_string($expression) ) {
            $alias = $expression;
        }
        $this->expressions[] = array('expr'=>$expression, 'alias'=>$alias);
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'SELECT';
        $this->buildLimit($query);
        $this->buildOffset($query);
        $this->buildExpressions($query);
        $this->buildFrom($query);
        $this->buildJoin($query);
        $this->buildCondition($query);
        $this->buildGroupBy($query);
        $this->buildHaving($query);
        $this->buildOrderBy($query);
        return implode(' ', $query);
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildExpressions( &$query ) {
        if ( empty($this->expressions) ) {
            $query[] = '*';
            return;
        }
    
        $db = $this->getDatabase();
        $expressionList = array();
        foreach ( $this->expressions as $expression ) {
            $expr = $expression['expr'];
            $expr = $db->quoteExpression($expr);
    
            $alias = $expression['alias'];
            if ( null === $alias ) {
                $expressionList[] = $expr;
            } else {
                $alias = $db->quoteColumnName($alias, ['uppercase'=>false]);
                $expressionList[] = "{$expr} AS {$alias}";
            }
        }
        $query[] = implode(', ', $expressionList);
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildOffset( &$query ) {
        if ( null === $this->offset ) {
            return;
        }
        $query[] = 'SKIP '.$this->offset;
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildLimit( &$query ) {
        if ( null === $this->limit ) {
            return;
        }
        $query[] = 'FIRST '.$this->limit;
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildFrom( &$query ) {
        if ( empty($this->tables) ) {
            $this->from('RDB$DATABASE');
        }
        parent::buildFrom($query);
    }
}