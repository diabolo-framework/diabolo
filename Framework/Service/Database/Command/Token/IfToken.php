<?php
namespace X\Service\Database\Command\Token;
use X\Service\Database\Command\Parser;
use X\Service\Database\DatabaseException;

/**
 * @example {{if value}} : value exists and value is ture
 * @example {{if value > 10}} : value greater that 10
 * @example {{if value = 'value'}} : value is equals to 'value'
 * @example {{if value = anotherValue}} : value is equals to anotherValue
 */
class IfToken {
    /** @var string */
    private $content = null;
    /** @var string */
    private $unparsedContent = '';
    /** @var string */
    private $dataName = null;
    /** @var string */
    private $operator = null;
    /** @var mixed */
    private $value = null;
    /** @var string */
    private $valueName = null;
    /** @var array */
    private $tokenTree = array();
    
    /** @param string $content */
    public function __construct( $content ) {
        $this->content = $content;
        $this->parseIfToken();
    }
    
    /** @return void */
    private function parseIfToken() {
        $newPosition = Parser::parserContentWalk($this->content, '}}') + 2;
        $head = substr($this->content, 0, $newPosition);
        $head = trim($head, '{}');
        $pattern = '@^if\s+?
        (?P<data>\w+) # name of data
        (\s+?
          (?P<operator>[=!<>])+\s+? # operator
          (
            (?P<value>\'\w+?\'|\d+?) # string or number
            |(?P<valueName>\w+?) # value name
          )
        )*$@isx';
        if ( !preg_match($pattern, $head, $headMatch) ) {
            throw new DatabaseException("if error : `{$head}`");
        }
        $this->dataName = $headMatch['data'];
        $this->operator = isset($headMatch['operator']) ? $headMatch['operator'] : null;
        $this->value = isset($headMatch['value']) ? $headMatch['value'] : null;
        $this->valueName = isset($headMatch['valueName']) ? $headMatch['valueName'] : null;
        
        $subnodeContent = substr($this->content, $newPosition);
        $this->tokenTree = Parser::parseContentToTokenTree(
            $subnodeContent,
            '{{endif}}',
            $this->unparsedContent
        );
    }
    
    /**
     * @param unknown $params
     * @param unknown $commandParams
     */
    public function getContent($params, &$commandParams) {
        # {{if value}}
        if ( null === $this->operator ) {
            if ( isset($params[$this->dataName]) && $params[$this->dataName]) {
                return $this->renderContent($params, $commandParams);
            } else {
                return '';
            }
        }
        
        $value = $this->value;
        if ( null !== $this->valueName ) {
            if ( !array_key_exists($this->valueName, $params) ) {
                throw new DatabaseException("no value for `{$this->valueName}`");
            }
            $value = $params[$this->valueName];
        }
        
        $isAbleToRender = false;
        switch ( $this->operator ){
        case '=' : $isAbleToRender = $params[$this->dataName] == $value; break;
        case '!=': $isAbleToRender = $params[$this->dataName] != $value; break;
        case '>' : $isAbleToRender = $params[$this->dataName] > $value; break;
        case '>=': $isAbleToRender = $params[$this->dataName] >= $value; break;
        case '<' : $isAbleToRender = $params[$this->dataName] < $value; break;
        case '<=': $isAbleToRender = $params[$this->dataName] <= $value; break;
        default : throw new DatabaseException("unsupported if operator `{$this->operator}`");
        }
        if ( $isAbleToRender ) {
            return $this->renderContent($params, $commandParams);
        } else {
            return '';
        }
    }
    
    /** @return string */
    private function renderContent( $params, &$commandParams ) {
        $command = array();
        foreach ( $this->tokenTree as $token ) {
            $command[] = $token->getContent($params, $commandParams);
        }
        return implode('', $command);
    }
    
    /** @return string */
    public function getUnparsedContent() {
        return $this->unparsedContent;
    }
}