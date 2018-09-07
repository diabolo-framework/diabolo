<?php
namespace X\Service\Mail;
use X\Core\Service\XService;
use X\Service\Mail\Handler\MailHandler;
/**
 * The service class
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Service extends XService {
    /** @var array */
    protected $mailers = array();
    
    /**
     * @param unknown $name
     * @throws MailException
     * @return \X\Service\Mail\Handler\MailHandler
     */
    public function getMailer( $name ) {
        if ( !$this->hasMailer($name) ) {
            throw new MailException("mailer `{$name}` does not exists.");
        }
        
        $mailerClass = $this->mailers[$name]['class'];
        $mailer = new $mailerClass($this->mailers[$name]);
        return $mailer;
    }
    
    /**
     * @param unknown $name
     * @return unknown
     */
    public function hasMailer( $name ) {
        return isset($this->mailers[$name]);
    }
}