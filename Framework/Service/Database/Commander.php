<?php
namespace X\Service\Database;
use X\Service\Database\Commander\Command;
class Commander {
    /** @var array The path to search commands */
    private static $paths = array();
    /** @var self */
    private static $commander = null;
    /** @var Command[] */
    private $commands = array( 
        # name => Command
    );
    
    /**
     * add command searching path
     * @param string $path 
     */
    public static function addPath( $path ) {
        self::$paths[] = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }
    
    /**
     * @param unknown $name
     * @param unknown $db
     * @return Command
     */
    public static function getCommand( $name, $db=null ) {
        if ( null === self::$commander ) {
            self::$commander = new self();
        }
        return self::$commander->getCommandByName( $name, $db );
    }
    
    /**
     * @param unknown $name
     * @param unknown $db
     * @return Command
     */
    private function getCommandByName($commandName, $db=null) {
        list($commandPath, $name) = $this->getCommandInfoByName($commandName);
        if ( isset($this->commands[$name]) ) {
            $command = $this->commands[$name];
            $command->setDatabase($db);
            return $command;
        }
        
        $commands = $this->parseCommandFile($commandPath);
        foreach ( $commands as $commandDef ) {
            $this->commands[$commandDef['name']] = new Command($commandDef);
        }
        if ( !isset($this->commands[$name]) ) {
            throw new DatabaseException("command `{$name}` does not exists");
        }
        return $this->getCommandByName($commandName, $db);
    }
    
    /** @return array */
    private function parseCommandFile( $filePath ) {
        $commands = explode('-- command : ', file_get_contents($filePath));
        array_shift($commands);
        
        foreach ( $commands as $index => $commandDef ) {
            $commandDef = trim($commandDef);
            $commandDef = str_replace("\r", "", $commandDef);
            $commandDef = explode("\n", $commandDef);
            
            $command = array();
            $command['name'] = array_shift($commandDef);
            $command['comment'] = array();
            while ( null !== ($line = array_shift($commandDef)) ) {
                if ( '-' !== $line[0] || '-' !== $line[1] ) {
                    array_unshift($commandDef, $line);
                    break;
                }
                $line = trim(ltrim($line, '-'));
                if ( preg_match('#^(?P<name>[a-zA-Z0-9]*?) : (?P<value>.*?)$#is', $line, $match) ) {
                    $command[$match['name']] = $match['value'];
                } else {
                    $command['comment'][] = $line;
                }
            }
            $command['comment'] = implode("\n", $command['comment']);
            $command['content'] = implode("\n", $commandDef);
            $commands[$index] = $command;
        }
        return $commands;
    }
    
    /** @return array */
    private function getCommandInfoByName( $commandName ) {
        if ( false === strpos($commandName, '.') ) {
            throw new DatabaseException("command name `{$commandName}` is not available");
        }
        
        list($file, $name) = explode('.', $commandName);
        $file = $file.'.sql';
        $filePath = null;
        foreach ( self::$paths as $path ) {
            $filePath = $path.$file;
            if ( file_exists($filePath) ) {
                break;
            }
            $filePath = null;
        }
        if ( null === $filePath ) {
            throw new DatabaseException("command `{$commandName}` does not exists");
        }
        return array($filePath, $name);
    }
}