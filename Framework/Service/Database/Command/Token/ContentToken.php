<?php
namespace X\Service\Database\Command\Token;
class ContentToken extends BaseToken {
    /** @var string */
    private $content = null;
    
    /** @param unknown $content */
    public function __construct( $content ) {
        $this->content = $content;
    }
    
    /**
     * @param array $params
     * @param array $commandParams
     */
    public function getContent( $params, &$commandParams ) {
        return $this->content;
    }
}