<?php
namespace X\Service\Database\Query\Firebird;
use X\Service\Database\Query\AlterTable as QueryAlterTable;
use X\Service\Database\DatabaseException;
use X\Service\Database\Table\Column;
class AlterTable extends QueryAlterTable {
    /**
     * @param string $newName
     * @return self
     */
    public function rename( $newName ) {
        throw new DatabaseException('firebird does not support rename table');
    }
    
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
    
    /** @param array $query */
    protected function buildActionAddColumn( &$query ) {
        $name = $this->getDatabase()->quoteColumnName($this->newColumnName);
        $defination = $this->newColumnDefination;
        if ( $defination instanceof Column ) {
            $defination->setDatabase($this->getDatabase());
        }
        $query[] = "ADD {$name} {$defination}";
    }
    
    /** @param array $query */
    protected function buildActionChagenColumn( &$query ) {
        $name = $this->getDatabase()->quoteColumnName($this->changeColumnName);
        $defination = $this->changeColumnDefination;
        if ( $defination instanceof Column ) {
            $defination->setDatabase($this->getDatabase());
        }
        $query[] = "ALTER COLUMN {$name} TYPE {$defination}";
    }
    
    /** @param array $query */
    protected function buildActionDropColumn( &$query ) {
        $name = $this->getDatabase()->quoteColumnName($this->dropColumnName);
        $query[] = "DROP {$name}";
    }
}