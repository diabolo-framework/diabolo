<?php
namespace X\Service\Database\Query;
use X\Service\Database\Table\Column;
use X\Service\Database\DatabaseException;
class CreateTable extends DatabaseQuery {
    /** @var string */
    private $name;
    /** @var Column[] */
    private $columns = array();
    
    /**
     * @param string $name
     * @return self
     */
    public function name( $name ) {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @param Column|string $column
     * @return self
     */
    public function addColumn( $column ) {
        $this->columns[] = $column;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'CREATE TABLE';
        $this->buildName($query);
        $this->buildColumns($query);
        return implode(' ', $query);
    }
    
    /**
     * @param string $query
     */
    private function buildName( &$query ) {
        $query[] = $this->getDatabase()->quoteTableName($this->name);
    }
    
    /**
     * @param string $query
     */
    private function buildColumns( &$query ) {
        $query[] = '(';
        
        $database = $this->getDatabase();
        $columnList = array();
        foreach ( $this->columns as $column ) {
            if ( $column instanceof Column ) {
                $column->setDatabase($database);
                $columnList[] = $column->toString();
            } else if ( is_string($column) ) {
                $columnList[] = $column;
            } else {
                throw new DatabaseException("unsuppoerted column defination");
            }
        }
        $query[] = implode(', ', $columnList);
        $query[] = ')';
    }
    
    /**
     * @return integer
     */
    public function exec() {
        return $this->getDatabase()->exec($this->toString());
    }
}