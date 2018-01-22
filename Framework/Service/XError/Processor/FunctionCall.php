<?php
namespace X\Service\XError\Processor;
/**
 * Call a function or method when error happend.
 * @author Michael Luthor <michaelluthor@163.com>
 */
class FunctionCall extends Processor {
    /**
     * (non-PHPdoc)
     * @see \X\Service\XError\Util\Processor::process()
     */
    public function process() {
        $error = $this->getService()->getErrorInformation();
        $config = $this->getConfiguration();
        
        $handler = $config['callback'];
        if ( !is_callable($handler) ) {
            $this->getService()->throwAnotherException('function to error handler is not callable.');
        }
        call_user_func_array($handler, array($error));
    }
}