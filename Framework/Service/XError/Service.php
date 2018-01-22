<?php
namespace X\Service\XError;
use X\Service\XError\Processor\Processor;

/**
 * XError service.
 * @author  Michael Luthor <michaelluthor@163.com>
 * @version 0.0.0
 * @since   Version 0.0.0
 */
class Service extends \X\Core\Service\XService {
    /**
     * 服务名称
     * @var string
     */
    protected static $serviceName = 'XError';
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        
        $config = $this->getConfiguration();
        $handlers = $config->get('handlers', array());
        if ( empty($handlers) ) {
            return;
        }
        
        set_error_handler(array($this, 'errorHandler'), $config->get('types', E_ALL));
        set_exception_handler(array($this, 'exceptionHandler'));
    }
    
    /**
     * Error information array
     * @var array
     */
    private $errorInformation = array();
    
    /**
     * Get error information.
     * @return array
     */
    public function getErrorInformation() {
        return $this->errorInformation;
    }
    
    /**
     * The error handler to handle system errors.
     * @param integer $number The error code
     * @param string $message The message on error happens
     * @param string $file The file path of error includes
     * @param integer $line The line number of error happend.
     * @param array $context The context on error happend.
     * @return void
     */
    public function errorHandler( $number, $message, $file, $line, $context ) {
        $errorInfo = array(
            'code'    => $number,
            'message'   => $message,
            'file'      => $file,
            'line'      => $line,
            'context'   => $context,
            'exception' => null,
        );
        $this->errorInformation = $errorInfo;
        $this->processError();
    }
    
    /**
     * handle exception errors.
     * @param Exception $exception
     * @return void
     */
    public function exceptionHandler( $exception ) {
        $errorInfo = array(
            'code'      => $exception->getCode(),
            'message'   => $exception->getMessage(),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
            'context'   => null,
            'exception' => $exception,
        );
        $this->errorInformation = $errorInfo;
        $this->processError();
    }
    
    /**
     * do error processing, execute each error handler.
     * @return void
     */
    private function processError() {
        $handlers = $this->getConfiguration()->get('handlers', array());
        foreach ( $handlers as $handler ) {
            $handlerClass = '\\X\\Service\\XError\\Processor\\'.ucfirst($handler['handler']);
            if ( !class_exists($handlerClass, true) ) {
                $this->throwAnotherException("error process handler '{$handler['handler']}' is not available.");
            }
            $handlerInstance = new $handlerClass($this, $handler);
            if ( !($handlerInstance instanceof Processor) ) {
                $this->throwAnotherException("error process handler '{$handler['handler']}' is not available.");
            }
            $handlerInstance->process();
        }
        \X\Core\X::system()->stop();
    }
    
    /**
     * Throw a new exception without x-error service.
     * @param string $message
     * @throws Exception
     */
    public function throwAnotherException($message) {
        restore_error_handler();
        restore_exception_handler();
        throw new \Exception($message);
    }
}