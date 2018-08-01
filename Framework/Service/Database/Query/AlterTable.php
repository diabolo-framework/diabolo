<?php
namespace X\Service\Database\Query;
use X\Service\Database\Table\Column;

class AlterTable extends DatabaseQuery {
    /** @var string */
    protected $actionName = null;
    /** @var string */
    protected $table = null;
    /** @var string rename table name to new one */
    private $newTableName = null;
    /** @var string */
    private $newColumnName = null;
    /** @var string */
    private $newColumnDefination = null;
    /** @var string */
    private $dropColumnName = null;
    /** @var string */
    protected $changeColumnName = null;
    /** @var mixed */
    protected $changeColumnDefination = null;
    /** @var string */
    protected $changeColumnNewName = null;
    /** @var string */
    protected $newIndexName = null;
    /** @var array */
    protected $newIndexColumns = array();
    /** @var string */
    protected $dropIndexName = null;
    /** @var string */
    private $addFKName = null;
    /** @var array */
    private $addFKColumns = array();
    /** @var string */
    private $addFKRefTableName = null;
    /** @var array */
    private $addFKRefColumns = array();
    /** @var string */
    private $dropFKName = null;
    
    /**
     * @param string $name
     * @return self
     */
    public function table( $name ) {
        $this->table = $name;
        return $this;
    }
    
    /**
     * @param string $newName
     * @return self
     */
    public function rename( $newName ) {
        $this->actionName = 'RenameTable';
        $this->newTableName = $newName;
        return $this;
    }
    
    /**
     * @param string $name
     * @param string $defination
     * @return self
     */
    public function addColumn( $name, $defination ) {
        $this->actionName = 'AddColumn';
        $this->newColumnName = $name;
        $this->newColumnDefination = $defination;
        return $this;
    }
    
    /**
     * @param string $name
     * @return self
     */
    public function dropColumn( $name ) {
        $this->actionName = 'DropColumn';
        $this->dropColumnName = $name;
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $defination
     * @param string $newName
     * @return self
     */
    public function changeColumn( $name, $defination, $newName=null ) {
        $this->actionName = 'ChagenColumn';
        $this->changeColumnName = $name;
        $this->changeColumnDefination = $defination;
        $this->changeColumnNewName = $newName;
        return $this;
    }
    
    /**
     * @param string $name
     * @param string[] $columns
     * @return self
     */
    public function addIndex( $name, $columns ) {
        $this->actionName = 'AddIndex';
        $this->newIndexName = $name;
        $this->newIndexColumns = $columns;
        return $this;
    }
    
    /**
     * @param unknown $name
     * @return self
     */
    public function dropIndex( $name ) {
        $this->actionName = 'DropIndex';
        $this->dropIndexName = $name;
        return $this;
    }
    
    /**
     * @param unknown $column
     * @param unknown $refTable
     * @param unknown $refColumns
     * @return self
     */
    public function addForeignKey( $name, $columns, $refTable, $refColumns ) {
        $this->actionName = 'AddForeignKey';
        $this->addFKName = $name;
        $this->addFKColumns = $columns;
        $this->addFKRefTableName = $refTable;
        $this->addFKRefColumns = $refColumns;
        return $this;
    }
    
    /**
     * @param unknown $name
     * @return self
     */
    public function dropForeignKey( $name ) {
        $this->actionName = 'DropForeignKey';
        $this->dropFKName = $name;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Query\DatabaseQuery::toString()
     */
    public function toString() {
        $query = array();
        $query[] = 'ALTER TABLE';
        $query[] = $this->getDatabase()->quoteTableName($this->table);
        
        $action = 'buildAction'.$this->actionName;
        $this->$action($query);
        return implode(' ', $query);
    }
    
    /** @param array $query */
    private function buildActionRenameTable( &$query ) {
        $table = $this->getDatabase()->quoteTableName($this->newTableName);
        $query[] = "RENAME TO {$table}";
    }
    
    /** @param array $query */
    private function buildActionAddColumn( &$query ) {
        $name = $this->getDatabase()->quoteColumnName($this->newColumnName);
        $defination = $this->newColumnDefination;
        $query[] = "ADD COLUMN {$name} {$defination}";
    }
    
    /** @param array $query */
    private function buildActionDropColumn( &$query ) {
        $name = $this->getDatabase()->quoteColumnName($this->dropColumnName);
        $query[] = "DROP COLUMN {$name}";
    }
    
    /** @param array $query */
    protected function buildActionChagenColumn( &$query ) {
        $name = $this->getDatabase()->quoteColumnName($this->changeColumnName);
        $newName = $this->changeColumnNewName;
        if ( null === $newName ) {
            $newName = $this->changeColumnName;
        }
        $newName = $this->getDatabase()->quoteColumnName($newName);
        $defination = $this->changeColumnDefination;
        $query[] = "CHANGE COLUMN {$name} {$newName} {$defination}";
    }
    
    /** @param array $query */
    private function buildActionAddIndex( &$query ) {
        $db = $this->getDatabase();
        $name = $db->quoteTableName($this->newIndexName);
        $columns = array();
        foreach ( $this->newIndexColumns as $column ) {
            $columns[] = $db->quoteColumnName($column);
        }
        $columns = implode(', ', $columns);
        $query[] = "ADD INDEX {$name} ( {$columns} )";
    }
    
    /** @param array $query */
    private function buildActionDropIndex( &$query ) {
        $name = $this->getDatabase()->quoteColumnName($this->dropIndexName);
        $query[] = "DROP INDEX {$name}";
    }
    
    /** @param array $query */
    private function buildActionAddForeignKey( &$query ) {
        $db = $this->getDatabase();
        $name = $db->quoteTableName($this->addFKName);
        
        $columns = array_map(array($db,'quoteColumnName'), $this->addFKColumns);
        $columns = implode(', ', $columns);
        
        $refTable = $db->quoteTableName($this->addFKRefTableName);
        $refColumns = array_map(array($db, 'quoteColumnName'), $this->addFKRefColumns);
        $refColumns = implode(', ', $refColumns);
        
        $query[] = "ADD CONSTRAINT {$name}";
        $query[] = "FOREIGN KEY ( {$columns} )";
        $query[] = "REFERENCES {$refTable} ( {$refColumns} )";
    }
    
    /** @param array $query */
    private function buildActionDropForeignKey( &$query ) {
        $name = $this->getDatabase()->quoteTableName($this->dropFKName);
        $query[] = "DROP FOREIGN KEY {$name}";
    }
    
    /**
     * @return integer
     */
    public function exec() {
        return $this->getDatabase()->exec($this->toString());
    }
}