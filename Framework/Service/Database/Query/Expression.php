<?php 
namespace X\Service\Database\Query;
use X\Service\Database\Database;
class Expression {
    /** @var string */
    private $expression = null;
    
    /**
     * @param string $expression
     */
    public function __construct( $expression ) {
        $this->expression = $expression;
    }
    
    /** @var string */
    public function toString() {
        return $this->expression;
    }
    
    /** @var string */
    public function __toString() {
        return $this->expression;
    }
    
    /**
     * @param string $name
     * @return self
     */
    public static function count( $name='*' ) {
        return new self("COUNT({$name})");
    }
    
    /**
     * @param string $name
     * @param Database $db
     * @return self
     */
    public static function column( $name, Database $db ) {
        return new self($db->quoteExpression($name));
    }
}