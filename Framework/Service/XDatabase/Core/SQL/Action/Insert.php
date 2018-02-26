<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Action;

/**
 * 
 */
use X\Service\XDatabase\Core\Util\Exception;
use X\Service\XDatabase\Core\SQL\Util\DefaultValue;
use X\Service\XDatabase\Core\SQL\Util\ActionBase;
use X\Service\XDatabase\Core\SQL\Util\Expression;

/**
 * Insert action builder
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Insert extends ActionBase {
    /**
     * Set the table name which would insert data into.
     * @param stirng $table The name table to insert into
     * @return Insert
     */
    public function into( $table ){
        $this->tblName = $table;
        return $this;
    }
    
    /**
     * set values to insert action
     * @param array $values
     * @return \X\Service\XDatabase\Core\SQL\Action\Insert
     */
    public function values( $values ) {
        if ( empty($values) ) {
            throw new Exception('Unable to insert empty data.');
        }
        $this->values[] = $values;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Action\Base::getBuildHandlers() Base::getBuildHandlers()
     */
    protected function getBuildHandlers() {
        return array('action','into', 'value');
    }
    
    /**
     * Add action name into string.
     * @return Insert
     */
    protected function buildHandlerAction() {
        $this->sqlCommand[] = 'INSERT';
        return $this;
    }
    
    /**
     * The name of the table which would insert into. It can not be empty.
     * @var string
     */
    protected $tblName = null;
    
    /**
     * Get sql command part of insert table name. The method is called by
     * toString() method to build the whole command.
     * @return Insert
     */
    protected function buildHandlerInto() {
        if ( null === $this->tblName ) {
            throw new Exception('Table name can not be empty in insert action.');
        }
        $this->sqlCommand[] = 'INTO '.$this->quoteTableName($this->tblName);
        return $this;
    }
    
    /**
     * The data would be insert into the record.
     * @var array
     */
    protected $values = array();
    
    /**
     * Get the command part of sql string. this method would be called by 
     * toString() method.
     * @return Insert
     */
    protected function buildHandlerValue() {
        $columns = array();
        $group = array();
        
        foreach ( $this->values as $index => $record ) {
            $values = array();
            foreach ( $record as $columnName => $value ) {
                if ( !is_numeric($columnName) && 0===$index ) {
                    $columns[] = $this->quoteColumnName($columnName);
                }
                if ( $value instanceof DefaultValue ) {
                    $values[] = 'DEFAULT';
                } else if ( $value instanceof Expression ) {
                    $values[] = $value->toString();
                }  else {
                    $values[] = $this->quoteValue($value);
                }
            }
            $values = '('.implode(',', $values).')';
            $group[] = $values;
        }
        
        if ( !empty($columns) ) {
            $columns = '('.implode(',', $columns).')';
        } else {
            $columns = '';
        }
        $this->sqlCommand[] = $columns.' VALUES '.implode(',', $group);
        return $this;
    }
}