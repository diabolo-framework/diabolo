<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Func;
/**
 * 
 */
use X\Service\XDatabase\Core\SQL\Util\Func;
/**
 * Count
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Sum extends Func {
    /**
     * The column name to count
     * @var string
     */
    protected $column = null;
    
    /**
     * Initiate the count object by given column name.
     * @param string $column The column name to count
     * @return Max
     */
    public function __construct( $column ) {
        $this->column = $column;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Database\SQL\Func\Func::toString() Func::toString()
     */
    public function toString() {
        return 'SUM('.$this->quoteColumnName($this->column).')';
    }
}