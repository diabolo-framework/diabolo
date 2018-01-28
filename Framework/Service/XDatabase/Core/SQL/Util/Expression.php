<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Util;

/**
 * The expression class.
 * @author Michael Luthor <mihaelluthor@163.com>
 */
class Expression {
    /**
     * The expression string for sql query.
     * @var string
     */
    private $expression = null;
    
    /**
     * construce this instance.
     * @param string $expression
     */
    public function __construct($expression) {
        $this->expression = $expression;
    }
    
    /**
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Basic::toString()
     */
    public function toString() {
        return $this->expression;
    }
}