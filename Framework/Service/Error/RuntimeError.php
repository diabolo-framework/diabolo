<?php
namespace X\Service\Error;
class RuntimeError {
    /** @var string|int */
    private $code = null;
    /** @var string */
    private $message = null;
    /** @var string */
    private $file = null;
    /** @var int */
    private $line = null;
    /** @var mixed */
    private $context = null;
    /** @var \Exception */
    private $exception = null;
    
    /**
     * @param unknown $code
     * @return \X\Service\Error\RuntimeError
     */
    public function setCode( $code ) { 
        $this->code = $code;
        return $this; 
    }
    
    /**
     * @return string|number
     */
    public function getCode() {
        return $this->code;
    }
    
    /**
     * @param unknown $message
     * @return \X\Service\Error\RuntimeError
     */
    public function setMessage( $message ) { 
        $this->message = $message;
        return $this; 
    }
    
    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }
    
    /**
     * @param unknown $file
     * @return \X\Service\Error\RuntimeError
     */
    public function setFile( $file ) { 
        $this->file = $file;
        return $this; 
    }
    
    /**
     * @return string
     */
    public function getFile() {
        return $this->file;
    }
    
    /**
     * @param unknown $line
     * @return \X\Service\Error\RuntimeError
     */
    public function setLine( $line ) { 
        $this->line = $line;
        return $this; 
    }
    
    /**
     * @return number
     */
    public function getLine() {
        return $this->line;
    }
    
    /**
     * @param unknown $context
     * @return \X\Service\Error\RuntimeError
     */
    public function setContext( $context ) { 
        $this->context = $context;
        return $this; 
    }
    
    /**
     * @return mixed|unknown
     */
    public function getContext() {
        return $this->context;
    }
    
    /**
     * @param unknown $exception
     * @return \X\Service\Error\RuntimeError
     */
    public function setException( $exception ) { 
        $this->exception = $exception;
        $this->setCode($exception->getCode());
        $this->setMessage($exception->getMessage());
        $this->setFile($exception->getFile());
        $this->setLine($exception->getLine());
        return $this; 
    }
    
    /**
     * @return Exception
     */
    public function getException() {
        return $this->exception;
    }
}