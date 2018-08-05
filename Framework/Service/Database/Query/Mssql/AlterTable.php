<?php
namespace X\Service\Database\Query\Mssql;
use X\Service\Database\Query\AlterTable as QueryAlterTable;
use X\Service\Database\Table\Column;
class AlterTable extends QueryAlterTable {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        switch ( $this->actionName ) {
        case 'RenameTable': return $this->actionRenameTable();
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
        $query[] = "ADD {$name} {$defination}";
    }
    
    /** @param array $query */
    protected function buildActionChagenColumn( &$query ) {
        $name = $this->getDatabase()->quoteColumnName($this->changeColumnName);
        $defination = $this->changeColumnDefination;
        if ( $defination instanceof Column ) {
            $defination->setDatabase($this->getDatabase());
        }
        $query[] = "ALTER COLUMN {$name} {$defination}";
    }
    
    /** @param array $query */
    protected function actionRenameTable() {
        $query = array();
        $query[] = 'SP_RENAME';
        $query[] = $this->getDatabase()->quoteTableName($this->table);
        $query[] = ',';
        $query[] = $this->getDatabase()->quoteTableName($this->newTableName);
        return implode(' ', $query);
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
        $query[] = 'ON';
        $query[] = $db->quoteTableName($this->table);
        return implode(' ', $query);
    }
}