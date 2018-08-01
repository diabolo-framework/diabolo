<?php
namespace X\Service\Database\Query;
use X\Service\Database\DatabaseException;
class Update extends DatabaseLimitableQuery {
    /** @var string */
    protected $table = null;
    /** @var array */
    protected $values = array(
        # array('name'=>$name, 'value'=>$value)
    );
    
    /**
     * @param string $table
     * @return self
     */
    public function table( $table ) {
        $this->table = $table;
        return $this;
    }
    
    /**
     * @param array $values
     * @return self
     */
    public function values( $values ) {
        foreach ( $values as $key => $value ) {
            $this->values[] = array('name'=>$key,'value'=>$value);
        }
        return $this;
    }
    
    /**
     * @param mixed $name
     * @param mixed $value
     * @return self
     */
    public function set( $name, $value ) {
        $this->values[] = array('name'=>$name, 'value'=>$value);
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'UPDATE';
        $this->buildTable($query);
        $this->buildValues($query);
        $this->buildCondition($query);
        $this->buildOrderBy($query);
        $this->buildLimit($query);
        return implode(' ', $query);
    }
    
    /** @param array $query */
    protected function buildTable( &$query ) {
        if ( null === $this->table ) {
            throw new DatabaseException('no table specified on update query');
        }
        
        $table = $this->getDatabase()->quoteTableName($this->table);
        $query[] = "{$table}";
    }
    
    /** @param array $query */
    protected function buildValues( &$query ) {
        if ( empty($this->values) ) {
            throw new DatabaseException('no values specified on update query');
        }
        
        $db = $this->getDatabase();
        $valueList = array();
        foreach ( $this->values as $item ) {
            $expr = $item['name'];
            $expr = $db->quoteExpression($expr);
            
            $paramKey = $this->getParamKey($item['value']);
            $valueList[] = "{$expr} = {$paramKey}";
        }
        
        $query[] = 'SET '.implode(', ', $valueList);
    }
    
    /**
     * @param mixed $value
     * @return string
     */
    private function getParamKey( $value ) {
        if ( $value instanceof Expression ) {
            return $value->toString();
        }
        
        $paramsKey = ':qp'.count($this->queryParams);
        $this->queryParams[$paramsKey] = $value;
        return $paramsKey;
    }
    
    /**
     * @return number
     */
    public function exec() {
        return $this->getDatabase()->exec($this->toString(), $this->queryParams);
    }
}