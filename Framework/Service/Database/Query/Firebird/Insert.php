<?php
namespace X\Service\Database\Query\Firebird;
use X\Service\Database\DatabaseException;
use X\Service\Database\Query\Insert as QueryInsert;
use X\Service\Database\Query;
use X\Service\Database\Query\Expression;
class Insert extends QueryInsert {
    /**
     * @param array $query
     * @return void
     */
    protected function buildValues( &$query ) {
        if ( empty($this->data) ) {
            throw new DatabaseException('insert data can not be empty');
        }
        
        if ( 1 === count($this->data) ) {
            return parent::buildValues($query);
        }
    
        $columns = array_keys($this->data[0]);
        $columns = array_map(array($this->getDatabase(),'quoteColumnName'), $columns);
        $query[] = '( '.implode(', ', $columns).' )';
        
        $db = $this->getDatabase();
        $rowList = array();
        foreach ( $this->data as $row ) {
            $select = Query::select($db);
            foreach ( $row as $value ) {
                $select->expression(new Expression($db->quoteValue($value)));
            }
            $rowList[] = $select->toString();
        }
        $query[] = implode(' UNION ALL ', $rowList);
    }
}