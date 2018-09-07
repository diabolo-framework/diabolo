<?php
namespace X\Service\Mail;
class Mail {
    /** @var string */
    public $subject = 'No Subject';
    /** @var string */
    public $content = 'No Content';
    /** @var string */
    public $from = 'nobody@localhost';
    /** @var string */
    public $fromName = 'NOBODY';
    /** @var boolean */
    public $isHtml = false;
    /** @var string */
    public $charset = 'utf-8';
    /** @var string */
    public $replyTo = null;
    
    /** @var string */
    protected $date = null;
    /** @var string */
    protected $messageId = null;
    /** @var array */
    protected $attachments = array(
        #array('name'=>'file-name', 'path'=>'file-path'),
    );
    /** @var array */
    protected $recipients = array(
        # array('name'=>'sige', 'address'=>'example@xyz.com'),
    );
    /** @var array */
    protected $ccRecipients = array(
        # array('name'=>'sige', 'address'=>'example@xyz.com'),
    );
    /** @var array */
    protected $bccRecipients = array(
        # array('name'=>'sige', 'address'=>'example@xyz.com'),
    );
    
    /** @return string */
    public function getDate() {
        if ( null === $this->date ) {
            $this->date = date('D, j M Y H:i:s O');
        }
        return $this->date;
    }
    
    /** @return string */
    public function getMessageId() {
        if ( null === $this->messageId ) {
            $this->messageId = sprintf("<%s@%s>", md5(uniqid(time())), $this->hostName);
        }
        return $this->messageId;
    }
    
    /**
     * @param unknown $address
     * @param unknown $name
     * @return self
     */
    public function addRecipient($address, $name=null) {
        $this->recipients[$address] = array('name'=>$name, 'address'=>$address);
        return $this;
    }
    
    /** @return array */
    public function getRecipients() {
        return $this->recipients;
    }
    
    /**
     * @param unknown $address
     * @param unknown $name
     * @return self
     */
    public function addCCRecipient($address, $name=null) {
        $this->ccRecipients[$address] = array('name'=>$name, 'address'=>$address);
        return $this;
    }
    
    /** @return array */
    public function getCCRecipients() {
        return $this->ccRecipients;
    }
    
    /**
     * @param unknown $address
     * @param unknown $name
     * @return self
     */
    public function addBCCRecipient($address, $name=null) {
        $this->bccRecipients[$address] = array('name'=>$name, 'address'=>$address);
        return $this;
    }
    
    /** @return array */
    public function getBCCRecipients() {
        return $this->bccRecipients;
    }
    
    /**
     * @param unknown $name
     * @param unknown $path
     * @return self
     */
    public function addAttachment( $name, $path ) {
        if ( !file_exists($path) ) {
            throw new MailException('attachment `'.$path.'` does not exists');
        }
        $this->attachments[] = array('name'=>$name, 'path'=>$path);
        return $this;
    }
    
    /** @return array */
    public function getAttachments() {
        return $this->attachments;
    }
    
    /** @return boolean */
    public function hasAttachments() {
        return !empty($this->attachments);
    }
    
    /** @return void */
    public function send( $mailer ) {
        $service = Service::getService();
        if ( !$service->hasMailer($mailer) ) {
            throw new MailException('can not find mailer `'.$mailer.'`');
        }
        
        $mailer = $service->getMailer($mailer);
        $mailer->send($this);
    }
}