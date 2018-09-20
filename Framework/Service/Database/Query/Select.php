<?php
namespace X\Service\Database\Query;
use X\Service\Database\QueryResult;

/**
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Select extends DatabaseLimitableQuery {
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
    
    /** @var array */
    private $expressions = array(
        # array('expr'=>'expr', 'alias'=>'alias')
    );
    /** @var array */
    protected $tables = array(
        # array('table'=>'table', 'alias'=>'alias')
    );
    /** @var array */
    private $joins = array(
        # arrary('table'=>'table', 'alias'=>'alias', 'type'=>'type','condition'=>$condition)
    );
    /** @var array */
    private $groupByNames = array();
    /** @var mixed */
    private $havingCondition = null;
    /** @var offset */
    protected $offset = null;
    
    /** @var int fetch style */
    private $fetchStyle = QueryResult::FETCH_ASSOC;
    /** @var string class name to fetch into */
    private $fetchClassName = null;
    /** @var string name of releated active record */
    private $arClass = null;
    /** @var array */
    private $arFilters = array();
    /** @var boolean */
    private $arUseDefaultFilter = true;
    
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
     * @param unknown $table
     * @param unknown $condition
     * @param unknown $tableAlias
     * @return self
     */
    public function innerJoin( $table, $condition, $tableAlias=null ) {
        return $this->join(self::INNER_JOIN, $table, $condition, $tableAlias);
    }
    
    /**
     * @param unknown $table
     * @param unknown $condition
     * @param unknown $tableAlias
     * @return self
     */
    public function leftJoin( $table, $condition, $tableAlias=null ) {
        return $this->join(self::LEFT_JOIN, $table, $condition, $tableAlias);
    }
    
    /**
     * @param unknown $table
     * @param unknown $condition
     * @param unknown $tableAlias
     * @return self
     */
    public function rightJoin( $table, $condition, $tableAlias=null ) {
        return $this->join(self::RIGHT_JOIN, $table, $condition, $tableAlias);
    }
    
    /**
     * @param unknown $table
     * @param unknown $condition
     * @param unknown $tableAlias
     * @return self
     */
    public function fullJoin( $table, $condition, $tableAlias=null ) {
        return $this->join(self::FULL_JOIN, $table, $condition, $tableAlias);
    }
    
    /**
     * @param unknown $table
     * @param unknown $condition
     * @param unknown $tableAlias
     * @return self
     */
    public function crossJoin( $table, $condition, $tableAlias=null ) {
        return $this->join(self::CROSS_JOIN, $table, $condition, $tableAlias);
    }
    
    /**
     * @param unknown $table
     * @param unknown $condition
     * @param unknown $tableAlias
     * @return self
     */
    public function outerJoin( $table, $condition, $tableAlias=null ) {
        return $this->join(self::OUTER_JOIN, $table, $condition, $tableAlias);
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
     * @param string $className
     * @return self
     */
    public function setReleatedActiveRecord($className) {
        $this->arClass = $className;
        return $this;
    }
    
    /**
     * @param string $name
     * @return self
     */
    public function filter( $name ) {
        $this->arFilters[] = $name;
        return $this;
    }
    
    /**
     * @return self
     */
    public function withoutDefaultFileter() {
        $this->arUseDefaultFilter = false;
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
    protected function buildFrom( &$query ) {
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
    protected function buildJoin( &$query ) {
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
    protected function buildCondition( &$query ) {
        $condition = Condition::build();
        if ( null !== $this->condition ) {
            $condition->add($this->condition);
        }
        
        if ( null !== $this->arClass ) {
            $filters = $this->arFilters;
            if ( $this->arUseDefaultFilter ) {
                $filters[] = 'default';
            }
            foreach ( $filters as $filter ) {
                $filterCondition = call_user_func_array(array($this->arClass, 'getFilter'), array($filter));
                $condition->add($filterCondition);
            }
        }
        
        if ( !$condition->isEmpty() ) {
            $condition->setPreviousParams($this->queryParams);
            $condition->setDatabase($this->getDatabase());
            $query[] = 'WHERE '.$condition->toString();
        }
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildGroupBy( &$query ) {
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
    protected function buildHaving( &$query ) {
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
    protected function buildOffset( &$query ) {
        if ( null === $this->offset ) {
            return;
        }
        $query[] = 'OFFSET '.$this->offset;
    }
    
    /**
     * @param string $style
     * @return self
     */
    public function setFetchStyle( $style ) {
        $this->fetchStyle = $style;
        return $this;
    }
    
    /**
     * @param string $className
     * @return \X\Service\Database\QueryResult
     */
    public function setFetchClass( $className ) {
        $this->fetchClassName = $className;
        return $this;
    }
    
    
    /** @return \X\Service\Database\QueryResult */
    public function all() {
        $queryResult = $this->getDatabase()->query($this->toString(), $this->queryParams);
        $queryResult->setFetchStyle($this->fetchStyle);
        $queryResult->setFetchClass($this->fetchClassName);
        return $queryResult;
    }
    
    /**
     * @return array
     */
    public function one() {
        $this->limit(1);
        return $this->all()->fetch();
    }
    
    /**
     * @return integer
     */
    public function count() {
        $oldExpressions = $this->expressions;
        $this->expression(Expression::count(), 'RowCount');
        
        $queryResult = $this->getDatabase()->query($this->toString(), $this->queryParams);
        $counter = $queryResult->fetchAll();
        
        $this->expressions = $oldExpressions;
        return 1*$counter[0]['RowCount'];
    }
}