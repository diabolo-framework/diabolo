<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\ActiveRecord;

/**
 * Criteria class
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Criteria {
    /**
     * The condition for current query.
     * @var mixed
     */
    public $condition = null;
    
    /**
     * this value contains all order information
     * @var array
     */
    private $orders = array();
    
    /**
     * add order to current criteria.
     * @param mixed $expression
     * @param string $order
     * @return void
     */
    public function addOrder( $expression, $order=null ) {
        $this->orders[] = array('expression'=>$expression, 'order'=>$order);
    }
    
    /**
     * check if any order has been seted in this criteria.
     * @return boolean
     */
    public function hasOrder() {
        return !empty($this->orders);
    }
    
    /**
     * Get orders on this criteria.
     * @return array
     */
    public function getOrders() {
        return $this->orders;
    }
    
    /**
     * length of result
     * @var integer
     */
    public $limit = 0;
    
    /**
     * position of result started
     * @var integer
     */
    public $position = 0;
}