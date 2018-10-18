<?php
namespace X\Service\Database;
class QueryResult implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable {
    const FETCH_ASSOC = \PDO::FETCH_ASSOC;
    const FETCH_CLASS = \PDO::FETCH_CLASS;
    
    /** @var int fetch style */
    private $fetchStyle = \PDO::FETCH_ASSOC;
    /** @var string class name to fetch into */
    private $fetchClassName = null;
    
    /** @var \PDOStatement */
    private $resultStatement = null;
    /** @var array|false|null */
    private $resultItems = null;
    
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
    
    /**
     * @param \PDOStatement $result
     */
    public function __construct( \PDOStatement $result ) {
        $this->resultStatement = $result;
    }
    
    /**
     * @return array
     */
    public function fetchAll() {
        if ( null !== $this->resultItems ) {
            return $this->resultItems;
        }
        
        if ( self::FETCH_CLASS === $this->fetchStyle 
        && is_subclass_of($this->fetchClassName, ActiveRecord::class) ) {
            $items = $this->resultStatement->fetchAll(\PDO::FETCH_ASSOC);
            foreach ( $items as $index => $item ) {
                $model = new $this->fetchClassName();
                $model->applyData($item);
                $items[$index] = $model;
            }
            $this->resultItems = $items;
        } else if ( self::FETCH_CLASS === $this->fetchStyle ) {
            $this->resultItems = $this->resultStatement->fetchAll($this->fetchStyle, $this->fetchClassName);
        } else {
            $this->resultItems = $this->resultStatement->fetchAll($this->fetchStyle);
        }
        return $this->resultItems;
    }
    
    /**
     * fetch row data from result set. as default, row data returns as array, if 
     * fetchStyle has been set, the fetched row data will be applyed into target 
     * class as returned result. if there is not result in result statement, null
     * will be returned.
     * @return array|mixed|null
     */
    public function fetch() {
        if ( null !== $this->resultItems ) {
            return array_shift($this->resultItems);
        }
        
        if ( self::FETCH_CLASS === $this->fetchStyle
        && is_subclass_of($this->fetchClassName, ActiveRecord::class) ) {
            $item = $this->resultStatement->fetch(\PDO::FETCH_ASSOC);
            if ( false === $item ) {
                return null;
            }
            $model = new $this->fetchClassName();
            $model->applyData($item);
            return $model;
        } else if ( self::FETCH_CLASS === $this->fetchStyle ) {
            return $this->resultStatement->fetch($this->fetchStyle, $this->fetchClassName);
        } else {
            return $this->resultStatement->fetch($this->fetchStyle);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        $this->fetchAll();
        return isset($this->resultItems[$offset]);
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset){
        $this->fetchAll();
        return $this->resultItems[$offset];
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        throw new DatabaseException('query result is read only');
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        throw new DatabaseException('query result is read only');
    }
    
    /** */
    public function current () {
        $this->fetchAll();
        return current($this->resultItems);
    }
    
    /** */
    public function next () {
        $this->fetchAll();
        return next($this->resultItems);
    }
    
    /** */
    public function key () {
        $this->fetchAll();
        return key($this->resultItems);
    }
    
    /***/
    public function valid () {
        $this->fetchAll();
        return isset($this->resultItems[key($this->resultItems)]);
    }
    
    /***/
    public function rewind () {
        $this->fetchAll();
        return reset($this->resultItems);
    }
    
    /**
     * {@inheritDoc}
     * @see Countable::count()
     */
    public function count() {
        $this->fetchAll();
        return count($this->resultItems);
    }
    /**
     * {@inheritDoc}
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize() {
        return $this->fetchAll();
    }
}