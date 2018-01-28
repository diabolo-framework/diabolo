<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Util;
/**
 * 
 */
use X\Service\XDatabase\Core\SQL\Condition\Builder as ConditionBuilder;
use X\Service\XDatabase\Core\Util\Exception;
/**
 * ActionWithCondition
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
abstract class ActionWithCondition extends ActionBase {
    /**
     * This value contains the condition part of query.
     * @var string|\X\Database\SQL\Condition\Builder
     */
    protected $condition = null;
    
    /**
     * Add where condition to query command.
     * Here are some example here:
     * @param \X\Database\SQL\Condition\Builder|string $condition 
     * @return ActionWithCondition
     */
    public function where( $condition ) {
        $this->condition = $condition;
        return $this;
    }
    
    /**
     * Add order to query command.
     * @param string $name The name of column to ordered.
     * @param string $order The order for that column
     * @return ActionWithCondition
     */
    public function orderBy( $name, $order=null ) {
        $order = array('expr'=>$name, 'order'=>$order);
        $this->orders[] = $order;
        return $this;
    }
    
    /**
     * Set the limitation to command.
     * @param integer $limit The limitation to command
     * @return ActionWithCondition
     */
    public function limit( $limit ) {
        if ( !is_int($limit) ) {
            throw new Exception('Query limition must be a integer.');
        }
        $this->limit = $limit;
        return $this;
    }
    
    /**
     * Set the offset to command.
     * @param integer $offset The offset that effect record start from.
     * @return ActionWithCondition
     */
    public function offset( $offset ) {
        if ( !is_int($offset) ) {
            throw new Exception('Query offset must be a integer.');
        }
        $this->offset = $offset;
        return $this;
    }
    
    /**
     * Get condition string for query command.
     * This method is called by toString() method.
     * @return ActionWithCondition
     */
    protected function buildHandlerCondition() {
        if ( empty($this->condition) ) {
            return $this;
        }
    
        $condition = $this->condition;
        if ( !($this->condition instanceof ConditionBuilder ) ) {
            $condition = ConditionBuilder::build($this->condition);
        }
        $condition = $condition->toString();
        if ( !empty($condition) ) {
            $this->sqlCommand[] = 'WHERE '.$condition;
        }
        return $this;
    }
    
    /**
     * The orders for select command.
     *  @var array
     */
    protected $orders = array();
    
    /**
     * Check whether there is any order on query command.
     * @return boolean
     */
    protected function hasOrder() {
        return 0 < count($this->orders);
    }
    
    /**
     * Get order command part.
     * This method is called by toString() method.
     * @return ActionWithCondition
     */
    protected function buildHandlerOrder() {
        if ( !$this->hasOrder() ) {
            return $this;
        }
    
        $orders = array();
        foreach ( $this->orders as $order ) {
            $expr = $order['expr'];
            if ( $expr instanceof Func ) {
                $expr = $expr->toString();
            } elseif ( $expr instanceof Expression ) {
                $expr = '('.$expr.')';
            } else {
                $expr = $this->quoteTableName($expr);
            }
            $orders[] = $expr.' '.(empty($order['order']) ? '' : $order['order']);
        }
        $orders = array_map('trim', $orders);
        $this->sqlCommand[] = 'ORDER BY '.implode(',', $orders);
        return $this;
    }
    
    /**
     * The limitation of effected row.
     * @var integer
     */
    protected $limit = null;
    
    /**
     * Get limitation part of command stirng.
     * This method is called by toString() method.
     * @return ActionWithCondition
     */
    protected function buildHandlerLimit() {
        if ( empty($this->limit) ) {
            return $this;
        }
        $this->sqlCommand[] = 'LIMIT '.$this->limit;
        return $this;
    }
    
    /**
     * The offset that effected record start from.
     * @var integer
     */
    protected $offset = null;
    
    /**
     * Get offset part of command.
     * This method is called by toString() method.
     * @return SQLBuilderActionSelect
     */
    protected function buildHandlerOffset() {
        if ( empty($this->offset) ) {
            return $this;
        }
        $this->sqlCommand[] = 'OFFSET '.$this->offset;
        return $this;
    }
}