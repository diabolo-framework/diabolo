<?php
namespace X\Service\Database\Commander\Token;
use X\Service\Database\DatabaseException;
class ValueToken extends BaseToken {
    /** @var string */
    private $valueName = null;
    
    /** @param string $content */
    public function __construct( $content ) {
        $this->valueName = $content;
    }
    
    /**
     * @param array $params
     * @param array $commandParams
     */
    public function getContent( $params, &$commandParams ) {
        if ( !array_key_exists($this->valueName, $params) ) {
            throw new DatabaseException("no value for `{$this->valueName}`");
        }
        
        $key = $this->getCommandParamKey($commandParams, $params[$this->valueName]);
        return $key;
    }
}