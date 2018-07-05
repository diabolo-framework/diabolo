<?php
namespace X\Service\Database\Query;
abstract class DatabaseLimitableQuery extends DatabaseQuery {
    /** @var string sort type for asc */
    const SORT_ASC = 'ASC';
    /** @var string sort type for desc */
    const SORT_DESC = 'DESC';
    
    /** @var mixed */
    private $condition = null;
    /** @var integer */
    private $limit = null;
    /** @var array */
    private $orders = array(
        # array ('name' => 'expr', 'order'=> 'order')
    );
    
    /** @var array */
    protected $queryParams = array();
    
    /**
     * @param integer $limit
     * @return self
     */
    public function limit( $limit ) {
        $this->limit = $limit;
        return $this;
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildLimit( &$query ) {
        if ( null === $this->limit ) {
            return;
        }
        $query[] = 'LIMIT '.$this->limit;
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
     * @param array $query
     * @return void
     */
    protected function buildCondition( &$query ) {
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
     * @param mixed $name
     * @param string $order
     * @return self
     */
    public function orderBy( $name, $order ) {
        if ( SORT_DESC === $order ) {
            $order = self::SORT_DESC;
        } else if ( SORT_ASC === $order ) {
            $order = self::SORT_ASC;
        }
        $this->orders[] = array('name'=>$name, 'order'=>$order);
        return $this;
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildOrderBy( &$query ) {
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
}