<?php
namespace X\Service\Database\Query\Oracle;
use X\Service\Database\DatabaseException;
use X\Service\Database\Query\Insert as QueryInsert;
use X\Service\Database\Query;
class Insert extends QueryInsert {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        if ( 1 === count($this->data) ) {
            return parent::toString();
        }
        
        $query = array();
        $query[] = 'INSERT ALL';
        $this->buildMuitlValues($query);
        $query[] = Query::select($this->getDatabase())->toString();
        return implode(' ', $query);
    }
    
    /**
     * @param array $query
     * @return void
     */
    private function buildMuitlValues( &$query ) {
        if ( empty($this->data) ) {
            throw new DatabaseException('insert data can not be empty');
        }
        
        $db = $this->getDatabase();
        $rows = array();
        foreach ( $this->data as $rowData ) {
            $rowQuery = array();
            $rowQuery[] = 'INTO';
            $rowQuery[] = $db->quoteTableName($this->table);
            
            $columns = array_keys($rowData);
            $columns = array_map(array($db,'quoteColumnName'), $columns);
            $rowQuery[] = '( '.implode(', ', $columns).' )';
            $rowQuery[] = 'VALUES';
            
            $values = array();
            foreach ( $rowData as $value ) {
                $values[] = $this->getParamKey($value);
            }
            $rowQuery[] = '( '.implode(', ', $values). ' )';
            $rows[] = implode(' ', $rowQuery);
        }
        $query[] = implode(' ', $rows);
    }
}