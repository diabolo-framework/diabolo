<?php
namespace X\Service\Log\Logger;
use X\Core\Component\OptionalObject;
use X\Service\Log\LogContent;
/**
 *
 */
abstract class ALogger extends OptionalObject implements ILogger {
    /**
     * defautlt to log all levels
     * @var integer
     **/
    protected $logLevel = self::LV_TRACE;
    
    /**
     * log contents
     * @var LogContent[]
     */
    protected $contents = array();
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Component\OptionalObject::init()
     */
    protected function init() {
        parent::init();
        if ( is_string($this->logLevel) ) {
            $logLevelMap = array(
                'fatal' => self::LV_FATAL,
                'error' => self::LV_ERROR,
                'warn'  => self::LV_WARN,
                'info'  => self::LV_INFO,
                'debug' => self::LV_DEBUG,
                'trace' => self::LV_TRACE,
            );
            $this->logLevel = $logLevelMap[$this->logLevel];
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::log()
     */
    public function log( $content, $level=self::LV_INFO ) {
        if ( !$this->isLevelHighEnoughToLog($level) ) {
            return;
        }
        
        $log = new LogContent();
        $log->content = $content;
        $log->isSynced = false;
        $log->level = $level;
        $log->loggedAt = microtime(true);
        $this->contents[] = $log;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::getActiveLogs()
     */
    public function getActiveLogs() {
        return $this->contents;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::clean()
     */
    public function clean() {
        $this->contents = [];
    }
    
    /**
     * @param unknown $level
     * @return boolean
     */
    protected function isLevelHighEnoughToLog( $level ) {
        return $level >= $this->logLevel;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::fatal()
     */
    public function fatal( $content ) {
        $this->log($content, self::LV_FATAL);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::error()
     */
    public function error( $content ) {
        $this->log($content, self::LV_ERROR);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::warn()
     */
    public function warn( $content ) {
        $this->log($content, self::LV_WARN);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::info()
     */
    public function info( $content ) {
        $this->log($content, self::LV_INFO);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::debug()
     */
    public function debug( $content ) {
        $this->log($content, self::LV_DEBUG);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::trace()
     */
    public function trace( $content ) {
        $this->log($content, self::LV_TRACE);
    }
}