<?php
namespace X\Service\Error\Handler;
use X\Core\X;
use X\Service\Error\Service as ErrorService;
use X\Service\Error\ErrorException;
class Email extends ErrorHandler {
    /** @var string config */
    protected $template = 'default';
    /** @var boolean */
    protected $isHtml = true;
    
    /**
     * (non-PHPdoc)
     * @see \X\Service\XError\Util\Processor::process()
     */
    public function process() {
        $errorSerivce = ErrorService::getService();
        
        ob_start();
        ob_implicit_flush(false);
        if ( 'default' === $this->template ) {
            require $errorSerivce->getPath('View/Email.php');
        } else {
            $templatePath = X::system()->getPath($this->template);
            if ( !is_file($templatePath) ) {
                throw new ErrorException("error handler email template `{$this->template}` does not exists.");
            }
            require $templatePath;
        }
        
        $content = ob_get_clean();
        X::system()->mailRuntimeError(array(
            'content' => $content,
            'isHtml' => $this->isHtml,
        ));
    }
}