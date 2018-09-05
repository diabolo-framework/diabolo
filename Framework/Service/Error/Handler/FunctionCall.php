<?php
namespace X\Service\Error\Handler;
class FunctionCall extends ErrorHandler {
    /** @var callback config */
    protected $callback = null;
    
    /**
     * (non-PHPdoc)
     * @see \X\Service\XError\Util\Processor::process()
     */
    public function process() {
        if ( !is_callable($this->callback) ) {
            throw new \ErrorException('function to error handler is not callable.');
        }
        call_user_func_array($this->callback, array($this));
    }
}