<?php
namespace X\Service\Error;
use X\Core\X;
use X\Core\Service\XService;
use X\Service\Error\Handler\ErrorHandler;
/** */
class Service extends XService {
    /** @var array config */
    protected $handlers = array();
    /** @var int config */
    protected $types = E_ALL;
    /** @var boolean */
    protected $stopOnError = false;
    /** @var RuntimeError */
    private $error = null;
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        
        if ( empty($this->handlers) ) {
            return;
        }
        $this->setupErrorHandlers();
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::stop()
     */
    public function stop() {
        $this->restoreErrorHandlers();
        parent::stop();
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
    public function handleError( $number, $message, $file, $line, $context ) {
        $runtimeError = (new RuntimeError())
            ->setCode($number)
            ->setMessage($message)
            ->setFile($file)
            ->setLine($line)
            ->setContext($context);
        
        $this->error = $runtimeError;
        $this->processError();
    }
    
    /**
     * handle exception errors.
     * @param Exception $exception
     * @return void
     */
    public function handleException( $exception ) {
        $runtimeError = (new RuntimeError())
            ->setException($exception);
        
        $this->error = $runtimeError;
        $this->processError();
    }
    
    /**
     * do error processing, execute each error handler.
     * @return void
     */
    private function processError() {
        $this->restoreErrorHandlers();
        
        foreach ( $this->handlers as $handler ) {
            $handlerClass = $handler['class'];
            if ( !class_exists($handlerClass, true) ) {
                throw new ErrorException("error process handler '{$handler['handler']}' is not available.");
            }
            if ( !is_subclass_of($handlerClass, ErrorHandler::class) ) {
                throw new ErrorException("error process handler '{$handler['handler']}' is not available.");
            }
            
            $handlerInstance = new $handlerClass($this->error,$handler);
            $handlerInstance->process();
        }
        
        if ( $this->stopOnError ) {
            X::system()->stop();
        } else {
            $this->setupErrorHandlers();
        }
    }
    
    /** @return void */
    private function setupErrorHandlers() {
        set_error_handler(array($this, 'handleError'), $this->types);
        set_exception_handler(array($this, 'handleException'));
    }
    
    /** @return void */
    private function restoreErrorHandlers() {
        restore_error_handler();
        restore_exception_handler();
    }
}