<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Action;
/**
 * 
 */
use X\Service\XDatabase\Core\SQL\Util\Func;
use X\Service\XDatabase\Core\SQL\Util\ActionWithCondition;
use X\Service\XDatabase\Core\SQL\Condition\Builder as ConditionBuilder;
/**
 * Select action builder
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Select extends ActionWithCondition {
    /**
     * Add expression to action.
     * @param mixed $expression
     * @param string $name
     * @return \X\Service\XDatabase\Core\SQL\Action\Select
     */
    public function expression( $expression, $name=null ) {
        $this->expressions[] = array('expr'=>$expression, 'name'=>$name);
        return $this;
    }
    
    /**
     * @var unknown
     */
    private $distinct = false;
    
    /**
     * @return \X\Service\XDatabase\Core\SQL\Action\Select
     */
    public function distinct () {
        $this->distinct = true;
        return $this;
    }
    
    /**
     * set table to action
     * @param string $table The name of table to Select from.
     * @param string $alias
     * @return Select
     */
    public function from( $table, $alias=null ) {
        $references = array('table'=>$table, 'alias'=>$alias);
        $this->tableReferences[] = $references;
        return $this;
    }
    
    /**
     * set group information to action.
     * @param string $name The column name to group
     * @param string $order The order to that group
     * @return Select
     */
    public function groupBy( $name, $order=null ) {
        $group = array('name'=>$name, 'order'=>$order);
        $this->groups[] = $group;
        return $this;
    }
    
    /**
     * set having for action.
     * @param SQLConditionBuilder|string $condition The having condition to query
     * @return Select
     */
    public function having( $condition ) {
        $this->havingCondition = $condition;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Action\Base::getBuildHandlers() Base::getBuildHandlers()
     */
    protected function getBuildHandlers() {
        return array(
            'action','distinct','expression','table',
            'condition','group','order',
            'having','limit','offset');
    }
    
    /**
     * Add action name into query.
     * @return SQLBuilderActionSelect
     */
    protected function buildHandlerAction() {
        $this->sqlCommand[] = 'SELECT';
        return $this;
    }
    
    /**
     * @return void
     */
    protected function buildHandlerDistinct() {
        if ( $this->distinct ) {
            $this->sqlCommand[] = 'DISTINCT';
        }
        return $this;
    }
    
    /**
     * this value contains all expressions.
     * @var array
     */
    protected $expressions = array();
    
    /**
     * Get Select expression part of command string.
     * This method is called by toString() method
     * @return Select
     */
    protected function buildHandlerExpression() {
        $expressions = array();
        foreach ( $this->expressions as $expression ) {
            if ( $expression['expr'] instanceof Func ) {
                $tempExpr = $expression['expr']->toString();
            } else if ( '*' === $expression['expr'] ) {
                $tempExpr = '*';
            } else {
                $tempExpr = $this->quoteColumnName($expression['expr']);
            }
            
            if ( null !== $expression['name'] ) {
                $tempExpr = $tempExpr.' AS '.$this->quoteColumnName($expression['name']);
            }
            $expressions[] = $tempExpr;
        }
        if ( 0 === count($expressions) ) {
            $expressions = array('*');
        }
        $this->sqlCommand[] = implode(',', $expressions);
        return $this;
    }
    
    /**
     * this value contains all table inforamtions.
     * @var array
     */
    protected $tableReferences = array();
    
    /**
     * Get from part of command string.
     * This method is called by toString() method. 
     * @return Select
     */
    protected function buildHandlerTable() {
        if ( 0 == count($this->tableReferences)) return $this;
        
        $tables = array();
        foreach ( $this->tableReferences as $item ) {
            $reference = $this->quoteTableName($item['table']);
            if ( isset($item['alias']) ) {
                $reference = $reference.' AS '.$this->quoteTableName($item['alias']);
            }
            $tables[] = $reference;
        }
        $this->sqlCommand[] = 'FROM '.implode(',', $tables);
        return $this;
    }
    
    /**
     * this value contains all group informations
     * @var array
     */
    protected $groups = array();
    
    /**
     * add group information to sql command
     * @return Select
     */
    protected function buildHandlerGroup() {
        if ( 0 == count($this->groups) ) {
            return $this;
        }
        
        $groups = array();
        foreach ( $this->groups as $group ) {
            $groupItem = $this->quoteColumnName($group['name']);
            if ( null !== $group['order'] ) {
                $groupItem = $groupItem.' '.$group['order'];
            }
            $groups[] = $groupItem;
        }
        $this->sqlCommand[] = 'GROUP BY '.implode(',', $groups);
        return $this;
    }
    
    /**
     * this value hold having value.
     * @var string|SQLConditionBuilder
     */
    protected $havingCondition = null;
    
    /**
     * Get having condition part of command.
     * This method is called by toString() method.
     * @return Select
     */
    protected function buildHandlerHaving() {
        if ( empty($this->havingCondition) ) {
            return $this;
        }
        
        $condition = $this->havingCondition;
        if ( !($this->havingCondition instanceof ConditionBuilder ) ) {
            $condition = ConditionBuilder::build($this->havingCondition);
        }
        $condition = $condition->toString();
        $this->sqlCommand[] = 'HAVING '.$condition;
        return $this;
    }
}
