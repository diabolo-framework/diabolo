<?php
namespace X\Service\XError\Processor;
use X\Core\X;
/**
 * go to another page to display error information.
 * @author Michael Luthor <michaelluthor@163.com>
 */
class View extends Processor {
    /**
     * (non-PHPdoc)
     * @see \X\Service\XError\Util\Processor::process()
     */
    public function process() {
        $configuration = $this->getConfiguration();
        
        ob_start();
        ob_implicit_flush(false);
        $path = X::system()->getPath($configuration['path']);
        require $path;
        $content = ob_get_clean();
        echo $content;
    }
}