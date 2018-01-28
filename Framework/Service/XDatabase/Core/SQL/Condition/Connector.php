<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Condition;
/**
 * Connector
 * @author  Michael Luthor <michael.the.ranidae@gamil.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Connector {
    /**
     * The name of Connector "and"
     * @var string
     */
    const CONNECTOR_AND = 'AND';
    
    /**
     * The name of Connector "or"
     * @var string
     */
    const CONNECTOR_OR  = 'OR';
    
    /**
     * The current Connector value
     * @var string
     */
    protected $connector = 'AND';
    
    /**
     * Initiate the Connector by given Connector name.
     * @param string $connector The Connector name for initiation.
     * @return void
     */
    public function __construct( $connector ) {
        $this->connector = $connector;
    }
    
    /**
     * Conver current object to string
     * @return string
     */
    public function toString() {
        return $this->connector;
    }
    
    /**
     * condition connector "AND" or "&&"
     * @return \X\Service\XDatabase\Core\SQL\Condition\Connector
     */
    public static function cAnd() {
        return new Connector(self::CONNECTOR_AND);
    }
    
    /**
     * condition connector "OR" or "||"
     * @return \X\Service\XDatabase\Core\SQL\Condition\Connector
     */
    public static function cOr() {
        return new Connector(self::CONNECTOR_OR);
    }
}