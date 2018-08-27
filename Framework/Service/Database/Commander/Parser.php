<?php
namespace X\Service\Database\Commander;
use X\Service\Database\Commander\Token\ContentToken;
use X\Service\Database\Commander\Token\IfToken;
use X\Service\Database\Commander\Token\ForeachToken;
use X\Service\Database\Commander\Token\PlaceholderToken;
use X\Service\Database\Commander\Token\ValueToken;
use X\Service\Database\DatabaseException;

class Parser {
    /** @var string */
    private $content = null;
    /** @var array */
    private $tokenTree = null;
    
    /** @param string $content */
    public function __construct( $content ) {
        $this->content = $content;
    }
    
    /**
     * @param array $params
     * @param array $commandParams
     * @return string
     */
    public function getCommand ( $params=array(), &$commandParams=array() ) {
        if ( null === $this->tokenTree ) {
            $this->tokenTree = self::parseContentToTokenTree($this->content);
        }
        
        $command = array();
        foreach ( $this->tokenTree as $token ) {
            $command[] = $token->getContent($params, $commandParams);
        }
        return implode('', $command);
    }
    
    /** @return void */
    public static function parseContentToTokenTree( $content, $stopWord=null, &$unparsedContent=null ) {
        $tokenTree = array();
        while ( !empty($content) ) {
            if ( null!==$stopWord && 0 === strpos($content, $stopWord) ) {
                $content = substr($content, strlen($stopWord));
                break;
            }
            
            $newPosition = self::parserContentWalk($content, '{{');
            if ( 0 !== $newPosition ) {
                $tokenTree[] = new ContentToken(substr($content, 0, $newPosition));
                $content = substr($content, $newPosition);
                if ( empty($content) ) {
                    break;
                }
            }
            if ( null!==$stopWord && 0 === strpos($content, $stopWord) ) {
                $content = substr($content, strlen($stopWord));
                break;
            }
            
            $newPosition = self::parserContentWalk($content, '}}')+2;
            if ( $newPosition >= strlen($content) ) {
                throw new DatabaseException('can not find close tag `}}`');
            }
            
            $token = substr($content, 0, $newPosition);
            $token = trim($token, '{}');
            if ( preg_match('#(?P<name>[a-zA-Z0-9]*?)\\s#is', $token, $tokenMatch) ) {
                $commandName = $tokenMatch['name'];
                switch ( $commandName ) {
                case 'if' :
                    $token = new IfToken($content);
                    $content = $token->getUnparsedContent();
                    $tokenTree[] = $token;
                    break;
                case 'foreach' :
                    $token = new ForeachToken($content);
                    $content = $token->getUnparsedContent();
                    $tokenTree[] = $token;
                    break;
                }
            } else if ( '#' === $token[0] ) {
                $tokenTree[] = new PlaceholderToken($token);
                $content = substr($content, $newPosition);
            } else {
                $tokenTree[] = new ValueToken($token);
                $content = substr($content, $newPosition);
            }
        }
        
        if ( null !== $unparsedContent ) {
            $unparsedContent = $content;
        }
        
        return $tokenTree;
    }
    
    /**
     * @param string $content
     * @param string $stopWord
     * @return int
     */
    public static function parserContentWalk( $content, $stopWord ) {
        $position = strpos($content, $stopWord);
        if ( false === $position ) {
            $position = strlen($content);
        }
        return $position;
    }
}