<?php
namespace X\Service\Database\Query;
use X\Service\Database\DatabaseException;

class Insert extends DatabaseQuery {
    /** @var string */
    protected $table = null;
    /** @var array */
    protected  $data = array();
    
    /**
     * @param unknown $table
     * @return self
     */
    public function table( $table ) {
        $this->table = $table;
        return $this;
    }
    
    /**
     * @param array $data
     * @return self
     */
    public function value( $data ) {
        $this->data[] = $data;
        return $this;
    }
    
    /**
     * @param array[] $data
     * @return self
     */
    public function values( $data ) {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'INSERT';
        $this->buildTable($query);
        $this->buildValues($query);
        return implode(' ', $query);
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildTable( &$query ) {
        $query[] = 'INTO';
        $query[] = $this->getDatabase()->quoteTableName($this->table);
    }
    
    /**
     * @param array $query
     * @return void
     */
    protected function buildValues( &$query ) {
        if ( empty($this->data) ) {
            throw new DatabaseException('insert data can not be empty');
        }
        
        $columns = array_keys($this->data[0]);
        $columns = array_map(array($this->getDatabase(),'quoteColumnName'), $columns);
        $query[] = '( '.implode(', ', $columns).' )';
        $query[] = 'VALUES';
        
        $rowList = array();
        foreach ( $this->data as $row ) {
            $rowData = array();
            foreach ( $row as $value ) {
                $rowData[] = $this->getParamKey($value);
            }
            $rowList[] = '( '.implode(', ', $rowData). ' )';
        }
        $query[] = implode(',', $rowList);
    }
    
    /**
     * @return number
     */
    public function exec() {
        return $this->getDatabase()->exec($this->toString(), $this->queryParams);
    }
}