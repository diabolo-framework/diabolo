<?php
namespace X\Service\Database\Commander\Token;
use X\Service\Database\DatabaseException;
class PlaceholderToken extends BaseToken {
    /** @var string */
    private $placeholderName = null;
    
    /** @param string $content */
    public function __construct( $content ) {
        $this->placeholderName = substr($content, 1);
    }
    
    /**
     * @param array $params
     * @param array $commandParams
     */
    public function getContent( $params, &$commandParams ) {
        if ( !array_key_exists($this->placeholderName, $params) ) {
            throw new DatabaseException("no value for `{$this->placeholderName}`");
        }
        return $params[$this->placeholderName];
    }
}