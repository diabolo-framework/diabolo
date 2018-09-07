<?php
namespace X\Service\Mail\Handler;
use X\Service\Mail\Mail;
use X\Service\Mail\MailException;
class PhpMail extends MailHandler {
    /**
     * {@inheritDoc}
     * @see \X\Service\Mail\Handler\MailHandler::send()
     */
    public function send( Mail $mail ) {
        $contentSeparator = false;
        
        $to = $this->getAddressListString($mail->getRecipients());
        $subject = $mail->subject;
        $header = $this->getHeaders($mail, $contentSeparator);
        $message = $this->getBody($mail, $contentSeparator);
        $params = null;
        
        if ( ini_get('safe_mode') ) {
            $isSuccess = mail($to,$subject,$message,$header);
        } else {
            $isSuccess = mail($to,$subject,$message,$header, $params);
        }
        if ( !$isSuccess ) {
            throw new MailException('failed to send mail : '.error_get_last()['message']);
        }
    }
}