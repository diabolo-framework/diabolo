<?php
namespace X\Service\Log;
use X\Service\Log\Logger\ILogger;
/**
 * @property string $levelName
 * @property string $sapiName
 */
class LogContent {
    /** @var array */
    private static $levelNumNameMap = array(
        ILogger::LV_TRACE  => 'TRACE',
        ILogger::LV_DEBUG  => 'DEBUG',
        ILogger::LV_INFO   => 'INFO',
        ILogger::LV_WARN   => 'WARN',
        ILogger::LV_ERROR  => 'ERROR',
        ILogger::LV_FATAL  => 'FATAL',
    );
    
    /**
     * timestamp with micro seconds when log generated
     * @var integer
     */
    public $loggedAt = null;
    
    /**
     * content of log record
     * @var string
     */
    public $content = null;
    
    /**
     * name of log level
     * @var string
     */
    public $level = null;
    
    /**
     * is this log synced into storage
     * @var boolean
     * */
    public $isSynced = false;
    
    /**
     * get log attribtue by given name
     * @param string $name
     * @return mixed
     */
    public function getAttributeByName( $name ) {
        if ( property_exists($this, $name) ) {
            return $this->$name;
        }
        
        $getter = 'get'.ucfirst($name);
        if ( method_exists($this, $getter) ) {
            return $this->$getter();
        }
        throw new LogException("unable to get log attribute `{$name}`");
    }
    
    /**
     * @return string
     */
    public function getPrettyTime() {
        $microSec = explode('.', strval($this->loggedAt));
        return date('Y-m-d H:i:s',intval($this->loggedAt)).'.'.end($microSec);
    }
    
    /**
     * get log level name
     * @return string
     */
    public function getLevelName() {
        $level = $this->level;
        return isset(self::$levelNumNameMap[$level])
        ? self::$levelNumNameMap[$level]
        : $level;
    }
    
    /**
     * get current sapi name
     * @return string
     */
    public function getSapiName() {
        return php_sapi_name();
    }
}