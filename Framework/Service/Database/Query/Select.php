<?php
namespace X\Service\Database\Query;
/**
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Select extends DatabaseQuery {
    /** @var string join type for inner join */
    const INNER_JOIN = 'INNER';
    /** @var string join type for left join */
    const LEFT_JOIN = 'LEFT';
    /** @var string join type for right join */
    const RIGHT_JOIN = 'RIGHT';
    /** @var string join type for full join */
    const FULL_JOIN = 'FULL';
    /** @var string join type for cross join */
    const CROSS_JOIN = 'CROSS';
    /** @var string join type for outer join */
    const OUTER_JOIN = 'OUTER';
    
    /** @var string sort type for asc */
    const SORT_ASC = 'ASC';
    /** @var string sort type for desc */
    const SORT_DESC = 'DESC';
    
    /** @var array */
    private $expressions = array(
        # array('expr'=>'expr', 'alias'=>'alias')
    );
    /** @var array */
    private $tables = array(
        # array('table'=>'table', 'alias'=>'alias')
    );
    /** @var array */
    private $joins = array(
        # arrary('table'=>'table', 'alias'=>'alias', 'type'=>'type','condition'=>$condition)
    );
    /** @var array */
    private $orders = array(
        # array ('name' => 'expr', 'order'=> 'order')
    );
    /** @var mixed */
    private $condition = null;
    /** @var array */
    private $groupByNames = array();
    /** @var mixed */
    private $havingCondition = null;
    /** @var integer */
    private $limit = null;
    /** @var offset */
    private $offset = null;
    
    /** @var array */
    private $queryParams = array();
    
    /**
     * @param mixed $expression
     * @param string $alias
     * @return self
     */
    public function expression( $expression, $alias=null ) {
        $this->expressions[] = array('expr'=>$expression, 'alias'=>$alias);
        return $this;
    }
    
    /**
     * @param mixed $name
     * @param string $alias
     * @return self
     */
    public function column( $name, $alias=null ) {
        return $this->expression($name, $alias);
    }
    
    /**
     * @param mixed $name
     * @param string $alias
     * @return self
     */
    public function from( $name, $alias=null ) {
        $this->tables[] = array('table'=>$name, 'alias'=>$alias);
        return $this;
    }
    
    /**
     * @param mixed $name
     * @param string $alias
     * @return self
     */
    public function table( $name, $alias=null ) {
        return $this->from($name, $alias);
    }
    
    /**
     * @param mixed $condition
     * @return self
     */
    public function where( $condition ) {
        $this->condition = $condition;
        return $this;
    }
    
    /**
     * @param mixed $name
     * @return self
     */
    public function groupBy( $name ) {
        $this->groupByNames[] = $name;
        return $this;
    }
    
    /**
     * @param mixed $condition
     * @return self
     */
    public function having( $condition ) {
        $this->havingCondition = $condition;
        return $this;
    }
    
    /**
     * @param mixed $table
     * @param mixed $condition
     * @param string $type
     * @return self
     */
    public function join($type, $table, $condition, $tableAlias=null) {
        $this->joins[] = array(
            'table'=>$table, 
            'alias'=>$tableAlias, 
            'condition'=>$condition,
            'type'=>$type
        );
        return $this;
    }
    
    /**
     * @param mixed $name
     * @param string $order
     * @return self
     */
    public function orderBy( $name, $order ) {
        $this->orders[] = array('name'=>$name, 'order'=>$order);
        return $this;
    }
    
    /**
     * @param integer $limit
     * @return self
     */
    public function limit( $limit ) {
        $this->limit = $limit;
        return $this;
    }
    
    /**
     * @param integer $offset
     * @return self
     */
    public function offset( $offset ) {
        $this->offset = $offset;
        return $this;
    }
    
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
        $this->buildLimit($query);
        $this->buildOffset($query);
        return implode(' ', $query);
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildExpressions( &$query ) {
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
                $alias = $db->quoteColumnName($alias);
                $expressionList[] = "{$expr} AS {$alias}";
            }
        }
        $query[] = implode(', ', $expressionList);
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildFrom( &$query ) {
        if ( empty($this->tables) ) {
            return;
        }
        
        $db = $this->getDatabase();
        $tableList = array();
        foreach ( $this->tables as $table ) {
            $name = $table['table'];
            $name = $db->quoteTableName($name);
            
            $alias = $table['alias'];
            if ( null === $alias ) {
                $tableList[] = $name;
            } else {
                $alias = $db->quoteTableName($alias);
                $tableList[] = "{$name} {$alias}";
            }
        }
        
        $query[] = 'FROM '.implode(', ', $tableList);
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildJoin( &$query ) {
        if ( empty($this->joins) ) {
            return;
        }
        
        foreach ( $this->joins as $join ) {
            $table = $join['table'];
            $table = $this->getDatabase()->quoteTableName($table);
            
            $alias = $join['alias'];
            if ( null !== $alias ) {
                $table .= ' '.$this->getDatabase()->quoteTableName($alias);
            }
            
            $condition = $join['condition'];
            if ( !($condition instanceof Condition ) ) {
                $condition = Condition::build()->add($join['condition']);
            }
            $condition->setPreviousParams($this->queryParams);
            $condition->setDatabase($this->getDatabase());
            $condition = $condition->toString();
            
            $type = $join['type'];
            $query[] = "{$type} JOIN {$table} ON {$condition}";
        }
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildCondition( &$query ) {
        if ( null === $this->condition ) {
            return;
        }
        
        $condition = $this->condition;
        if ( !($condition instanceof Condition ) ) {
            $condition = Condition::build()->add($this->condition);
        }
        $condition->setPreviousParams($this->queryParams);
        $condition->setDatabase($this->getDatabase());
        $query[] = 'WHERE '.$condition->toString();
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildGroupBy( &$query ) {
        if ( empty($this->groupByNames) ) {
            return;
        }
        
        $groupByList = array();
        $db = $this->getDatabase();
        foreach ( $this->groupByNames as $groupByName ) {
            $groupByList[] = $db->quoteColumnName($groupByName);
        }
        $query[] = 'GROUP BY '.implode(', ', $groupByList);
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildHaving( &$query ) {
        if ( null === $this->havingCondition ) {
            return ;
        }
        
        $condition = $this->havingCondition;
        if ( !($condition instanceof Condition ) ) {
            $condition = Condition::build()->add($this->condition);
        }
        $condition->setPreviousParams($this->queryParams);
        $condition->setDatabase($this->getDatabase());
        $query[] = 'HAVING '.$condition->toString();
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildOrderBy( &$query ) {
        if ( empty($this->orders) ) {
            return;
        }
        
        $db = $this->getDatabase();
        $orderList = array();
        foreach ( $this->orders as $order ) {
            $name = $this->getDatabase()->quoteExpression($order['name']);
            $order = $order['order'];
            $orderList[] = "{$name} {$order}";
        }
        $query[] = 'ORDER BY '.implode(', ', $orderList);
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildLimit( &$query ) {
        if ( null === $this->limit ) {
            return;
        }
        $query[] = 'LIMIT '.$this->limit;
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildOffset( &$query ) {
        if ( null === $this->offset ) {
            return;
        }
        $query[] = 'OFFSET '.$this->offset;
    }
    
    /**
     * @return \X\Service\Database\QueryResult
     */
    public function all() {
        return $this->getDatabase()->query($this->toString(), $this->queryParams);
    }
    
    /**
     * @return array
     */
    public function one() {
        $this->limit(1);
        return $this->all()->fetch();
    }
}