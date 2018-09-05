<?php
namespace X\Service\Error\Handler;
use X\Core\X;
/**
 * go to another page to display error information.
 * @author Michael Luthor <michaelluthor@163.com>
 */
class View extends ErrorHandler {
    /** @var string */
    protected $path = null;
    
    /**
     * (non-PHPdoc)
     * @see \X\Service\XError\Util\Processor::process()
     */
    public function process() {
        ob_start();
        ob_implicit_flush(false);
        if ( file_exists($this->path) ) {
            require $this->path;
        } else {
            require X::system()->getPath($this->path);
        }
        $content = ob_get_clean();
        echo $content;
    }
}