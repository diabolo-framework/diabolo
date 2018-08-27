<?php
namespace X\Service\Database\Commander\Token;
use X\Service\Database\Commander\Parser;
use X\Service\Database\DatabaseException;

class ForeachToken {
    /** @var string */
    private $content = null;
    /** @var array */
    private $tokenTree = null;
    /** @var string */
    private $dataName = null;
    /** @var string */
    private $keyName = null;
    /** @var string */
    private $valueName = null;
    /** @var string */
    private $unparsedContent = '';
    
    
    /** @param unknown $content */
    public function __construct( $content ) {
        $this->content = $content;
        $this->parserSubNode();
    }
    
    /** @return void */
    private function parserSubNode() {
        $newPosition = Parser::parserContentWalk($this->content, '}}') + 2;
        $head = substr($this->content, 0, $newPosition);
        
        $head = trim($head, '{}');
        $pattern = '#^foreach\s+?(?P<data>\w+?)\s+?as(\s+?(?P<key>\w+?)\s+?=>)*\s+?(?P<value>\w+?)$#is';
        if ( !preg_match($pattern, $head, $headMatch) ) {
            throw new DatabaseException("foreach error : `{$head}`");
        }
        $this->dataName = $headMatch['data'];
        $this->keyName = isset($headMatch['key']) ? $headMatch['key'] : null;
        $this->valueName = $headMatch['value'];
        
        $subnodeContent = substr($this->content, $newPosition);
        $this->tokenTree = Parser::parseContentToTokenTree(
            $subnodeContent, 
            '{{endforeach}}', 
            $this->unparsedContent
        );
    }
    
    /**
     * @param unknown $params
     * @param unknown $commandParams
     */
    public function getContent ( $params, &$commandParams ) {
        if ( !array_key_exists($this->dataName, $params) ) {
            throw new DatabaseException("no value for {$this->dataName} in foreach");
        }
        
        $content = array();
        $data = $params[$this->dataName];
        foreach ( $data as $key => $value ) {
            if ( null !== $this->keyName ) {
                $params[$this->keyName] = $key;
            }
            $params[$this->valueName] = $value;
            
            $particle = array();
            foreach ( $this->tokenTree as $token ) {
                $particle[] = $token->getContent($params, $commandParams);
            }
            $content[] = implode('', $particle);
        }
        return implode('', $content);
    }
    
    /** @return string */
    public function getUnparsedContent() {
        return $this->unparsedContent;
    }
}