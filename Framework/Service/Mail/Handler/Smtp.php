<?php
namespace X\Service\Mail\Handler;
use X\Service\Mail\Mail;
use X\Service\Mail\MailException;
class Smtp extends MailHandler {
    /** @var string */
    protected $host = null;
    /** @var string */
    protected $localhostName = 'localhost';
    /** @var int */
    protected $port = 25;
    /** @var string */
    protected $username = null;
    /** @var string */
    protected $password = null;
    /** @var boolean  */
    protected $isAuthRequired = false;
    /** @var int */
    protected $timeOut = 30;
    
    /** @var resource */
    private $connection = null;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Mail\Handler\MailHandler::send()
     */
    public function send( Mail $mail ) {
        $this->connect();
        $this->hello();
        $this->authorize();
        
        $contentSeparator = null;
        $headers = $this->getHeaders($mail, $contentSeparator);
        $body = $this->getBody($mail, $contentSeparator);
        
        foreach ( $mail->getRecipients() as $recipient ) {
            $this->sendContent($recipient['address'], $headers, $body);
        }
        
        $this->quit();
        $this->close();
    }
    
    /** connect to smtp server */
    private function connect() {
        $this->connection = fsockopen($this->host, $this->port, $errno, $errmsg, $this->timeOut);
        if ( 0 !== $errno ) {
            throw new MailException("failed to connect to host {$this->host}:{$this->port} : {$errmsg}");
        }
        if ( false === $this->connection ) {
            throw new MailException("faile to init sock to {$this->host}:{$this->port}");
        }
        
        $response = fgets($this->connection);
        if ( '2'!==$response[0] && '2'!==$response[1] && '0'!==$response[2] ) {
            throw new MailException('smtp server error : '.$response );
        }
    }
    
    /** send command to server */
    private function command( $name, $params='' ) {
        $cmd = trim($name.' '.$params)."\r\n";
        fputs($this->connection, $cmd);
        
        $response = fgets($this->connection);
        $responseCode = intval($response[0].$response[1].$response[2]);
        switch ( $responseCode ) {
        case 334 : 
        case 354 : 
        case 235 : 
        case 250 : /* NOTHING HERE, COMMAND SUCCESSED*/ break ;
        default : throw new MailException('smtp server error : '.$response);
        }
    }
    
    /** send content */
    private function write( $content ) {
        fputs($this->connection, $content);
    }
    
    /** command hello */
    private function hello() {
        $this->command('HELO', $this->localhostName);
    }
    
    /** login into system */
    private function authorize() {
        if ( !$this->isAuthRequired ) {
            return;
        }
        
        $this->command('AUTH LOGIN', base64_encode($this->username));
        $this->command('', base64_encode($this->password));
    }
    
    /** send mail content */
    private function sendContent($address, $header, $body) {
        $this->command('MAIL FROM:', '<'.$this->username.'>');
        $this->command('RCPT TO:', '<'.$address.'>');
        
        $this->command('DATA');
        $this->write($header."\r\n".$body);
        $this->write("\r\n.\r\n");
    }
    
    /** quit */
    private function quit() {
        $this->command('QUIT');
    }
    
    /** close the connection */
    private function close() {
        fclose($this->connection);
    }
}