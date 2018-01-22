<?php
namespace X\Service\XError\Processor;
use X\Core\X;
use X\Service\XError\Processor\Processor;
use X\Service\XMail\Service as XMailService;
/**
 * Send email to error information recivers.
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Email extends Processor {
    /**
     * (non-PHPdoc)
     * @see \X\Service\XError\Util\Processor::process()
     */
    public function process() {
        $errorService = $this->getService();
        $error = $errorService->getErrorInformation();
        $config = $this->getConfiguration();
        
        /* @var $mailService XMailService */
        $mailService = XMailService::getService();
        $mail = $mailService->create($config['subject'].':'.$error['message']);
        
        ob_start();
        ob_implicit_flush(false);
        extract(array('error'=>$error), EXTR_OVERWRITE);
        if ( 'default' === $config['template'] ) {
            require $errorService->getPath('View/Email.php');
        } else {
            $templatePath = X::system()->getPath($config['template']);
            if ( is_file($templatePath) ) {
                require $templatePath;
            } else {
                $errorService->throwAnotherException("error handler email template `{$config['template']}` does not exists.");
            }
        }
        $mail->Body = ob_get_clean();
        if ( 'default' !== $config['template'] ) {
            $mail->isHTML($config['isHtml']);
        }
        
        foreach ( $config['recipients'] as $name => $address ) {
            $mail->addAddress($address, $name);
        }
        
        $mail->setHandler($config['mail_handler']);
        if ( false === $mail->send() ) {
            $errorService->throwAnotherException('Unable to send error report to email.');
        }
    }
}