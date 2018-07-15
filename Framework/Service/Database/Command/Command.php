<?php
namespace X\Service\Database\Command;
use X\Service\Database\Database;
class Command {
    /** @var string */
    private $name = null;
    /** @var string */
    private $returnType = null;
    /** @var string */
    private $content = null;
    /**
     * @param unknown $defination
     */
    public function __construct( $defination ) {
        
    }
    
    public function setDatabase( Database $db ) {}
}