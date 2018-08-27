<?php
namespace X\Service\Database\Commander;
use X\Service\Database\Database;
use X\Service\Database\Service;
class Command {
    /** @var string */
    private $name = null;
    /** @var string */
    private $returnType = null;
    /** @var string */
    private $comment = null;
    /** @var string */
    private $content = null;
    /** @var Database */
    private $database = null;
    /** @var Parser */
    private $parser = null;
    
    /**
     * @param unknown $defination
     */
    public function __construct( $defination ) {
        $this->name = $defination['name'];
        $this->returnType = isset($defination['return']) ? $defination['return'] : null;
        $this->comment = isset($defination['comment']) ? $defination['comment'] : null;
        $this->content = $defination['content'];
        $this->parser = new Parser($this->content);
    }
    
    /**
     * @param Database $db
     * @return self
     */
    public function setDatabase( Database $db ) {
        $this->database = Service::getService()->getDB($db);
        return $this;
    }
    
    /**
     * @param array $params
     * @return number
     */
    public function exec( $params=array() ) {
        $commandParams = array();
        $command = $this->parser->getCommand($params, $commandParams);
        return $this->database->exec($command, $commandParams);
    }
    
    /**
     * @param array $params
     * @return \X\Service\Database\QueryResult
     */
    public function query( $params=array() ) {
        $commandParams = array();
        $command = $this->parser->getCommand($params, $commandParams);
        return $this->database->query($command, $commandParams);
    }
}