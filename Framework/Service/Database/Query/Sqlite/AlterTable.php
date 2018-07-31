<?php
namespace X\Service\Database\Query\Sqlite;
use X\Service\Database\Table\Column;
class AlterTable extends \X\Service\Database\Query\AlterTable {
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
}