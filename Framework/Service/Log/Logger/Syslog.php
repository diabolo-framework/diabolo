<?php
namespace X\Service\Log\Logger;
use X\Service\Log\LogContent;
class Syslog extends ALogger {
    /**
     * The string ident is added to each message.
     * @var string
     */
    protected $ident = null;
    /**
     * The facility argument is used to specify what type of program is logging the message.
     * @var integer
     */
    protected $facility = LOG_USER;
    
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Component\OptionalObject::init()
     */
    protected function init() {
        parent::init();
        openlog($this->ident, LOG_PID, $this->facility);
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
        $log->isSynced = true;
        $log->level = $level;
        $log->loggedAt = microtime(true);
        $this->contents[] = $log;
        
        $levelMap = array(
            self::LV_FATAL => LOG_EMERG,
            self::LV_ERROR => LOG_ERR,
            self::LV_WARN => LOG_WARNING,
            self::LV_INFO => LOG_INFO,
            self::LV_DEBUG => LOG_DEBUG,
            self::LV_TRACE => LOG_DEBUG,
        );
        syslog($levelMap[$level], $content);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::sync()
     */
    public function sync() {
        return;
    }
}