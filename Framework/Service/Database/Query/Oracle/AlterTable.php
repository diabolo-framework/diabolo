<?php
namespace X\Service\Database\Query\Oracle;
use X\Service\Database\Query\AlterTable as QueryAlterTable;
use X\Service\Database\Table\Column;
use X\Service\Database\DatabaseException;
class AlterTable extends QueryAlterTable {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\AlterTable::toString()
     */
    public function toString() {
        switch ( $this->actionName ) {
        case 'AddIndex': return $this->actionAddIndex();
        case 'DropIndex': return $this->actionDropIndex();
        default: return parent::toString();
        }
    }
    
    /** @param array $query */
    protected function buildActionAddColumn( &$query ) {
        $name = $this->getDatabase()->quoteColumnName($this->newColumnName);
        $defination = $this->newColumnDefination;
        if ( $defination instanceof Column ) {
            $defination->setDatabase($this->getDatabase());
        }
        $query[] = "ADD ( {$name} {$defination} )";
    }
    
    /** @param array $query */
    protected function buildActionChagenColumn( &$query ) {
        if ( null !== $this->changeColumnNewName ) {
            throw new DatabaseException('oracle does not support changing column name while changing column');
        }
        
        $name = $this->getDatabase()->quoteColumnName($this->changeColumnName);
        $defination = $this->changeColumnDefination;
        if ( $defination instanceof Column ) {
            $defination->setDatabase($this->getDatabase());
        }
        $query[] = "MODIFY {$name} {$defination}";
    }
    
    /**
     * @return string
     */
    protected function actionAddIndex( ) {
        $db = $this->getDatabase();
        
        $query = array();
        $query[] = 'CREATE INDEX';
        $query[] = $db->quoteTableName($this->newIndexName);
        $query[] = 'ON';
        $query[] = $db->quoteTableName($this->table);
        
        $columns = array();
        foreach ( $this->newIndexColumns as $column ) {
            $columns[] = $db->quoteColumnName($column);
        }
        $columns = implode(', ', $columns);
        $query[] = "( {$columns} )";
        return implode(' ', $query);
    }
    
    /**
     * @return string
     */
    protected function actionDropIndex() {
        $db = $this->getDatabase();
        
        $query = array();
        $query[] = 'DROP INDEX';
        $query[] = $db->quoteTableName($this->dropIndexName);
        return implode(' ', $query);
    }
}