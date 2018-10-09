<?php
namespace X\Service\Log;
use X\Core\X;
use X\Core\Service\XService;
use X\Service\Log\Logger\ILogger;
class Service extends XService {
    /** @var string */
    protected $defaultLogger = null;
    /** @var array */
    protected $loggers = array();
    /** @var ILogger[] */
    private $loggerInstances = array();
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        X::system()->registerMagicHandler('log',array($this, 'log'));
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::stop()
     */
    public function stop() {
        X::system()->unregisterMagicHandler('log');
        foreach ( $this->loggerInstances as $logger ) {
            $logger->sync();
            $logger->clean();
        }
        parent::stop();
    }
    
    /**
     * @param unknown $level
     * @param unknown $content
     */
    public function log ( $content, $level=ILogger::LV_INFO ) {
        $this->getLogger($this->defaultLogger)->log($content, $level);
    }
    
    /**
     * @param unknown $name
     * @return ILogger
     */
    public function getLogger( $name ) {
        if ( !isset($this->loggers[$name]) ) {
            throw new LogException("logger `{$name}` does not exists");
        }
        if ( !isset($this->loggerInstances[$name]) ) {
            $loggerClass = $this->loggers[$name]['logger'];
            $logger = new $loggerClass($this->loggers[$name]);
            $this->loggerInstances[$name] = $logger;
        }
        return $this->loggerInstances[$name];
    }
}