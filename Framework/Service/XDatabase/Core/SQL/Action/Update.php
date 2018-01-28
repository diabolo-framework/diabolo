<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Action;
/**
 * 
 */
use X\Service\XDatabase\Core\SQL\Util\ActionWithCondition;
use X\Service\XDatabase\Core\Util\Exception;
use X\Service\XDatabase\Core\SQL\Util\Expression;
/**
 * Update action builder
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @version 0.0.0
 * @since   0.0.0
 */
class Update extends ActionWithCondition {
    /**
     * Set Table to Update
     * @param string $table The table's name to Update
     * @return Update
     */
    public function table( $table ) {
        $this->tableReference = $table;
        return $this;
    }
    
    /**
     * Set values for query to Update.
     * @param array $values The values to Update.
     * @return Update
     */
    public function values( $values ) {
        if ( empty($values) ) {
            throw new Exception('updated value could not be empty.');
        }
        $this->values = $values;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Action\Base::getBuildHandlers() Base::getBuildHandlers()
     */
    protected function getBuildHandlers() {
        return array('action','table','value','condition','limit','order');
    }
    
    /**
     * The table reference. 
     * @var string
     */
    protected $tableReference = null;
    
    /**
     * Add table string into query.
     * @return Update
     */
    protected function buildHandlerTable() {
        if ( null === $this->tableReference ) {
            throw new Exception('Table reference can not be empty.');
        }
        $this->sqlCommand[] = $this->quoteTableName($this->tableReference);
        return $this;
    }
    
    /**
     * The updated values 
     * @var array
     */
    protected $values = array();
    
    /**
     * Add value string into query.
     * @return Update
     */
    protected function buildHandlerValue() {
        $changes = array();
        foreach ( $this->values as $name => $value ) {
            $column = $this->quoteColumnName($name);
            if ( $value instanceof Expression ) {
                $value = $value->toString();
            } else {
                $value = $this->quoteValue($value);
            }
            
            $changes[] = $column.'='.$value;
        }
        $this->sqlCommand[] = 'SET '.implode(',', $changes);
        return $this;
    }
    
    /**
     * Add action name into query
     * @return Update
     */
    protected function buildHandlerAction() {
        $this->sqlCommand[] = 'UPDATE';
        return $this;
    }
}