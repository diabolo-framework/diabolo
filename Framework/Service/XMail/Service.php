<?php
namespace X\Service\XMail;
use X\Service\XMail\Core\Mailer;
/**
 * The service class
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Service extends \X\Core\Service\XService {
    /**
     * 服务名称
     * @var string
     */
    protected static $serviceName = 'XMail';
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        
        $path = $this->getPath('Core/PHPMailer/PHPMailerAutoload.php');
        require_once $path;
    }
    
    /**
     * 创建邮件
     * @param string $subject 邮件主题
     * @return \X\Service\XMail\Core\Mailer
     */
    public function create($subject) {
        $mailer = new Mailer();
        $mailer->Subject = $subject;
        $mailer->CharSet = 'UTF-8';
        return $mailer;
    }
    
    /**
     * 发送文本邮件
     * @param string $address
     * @param string $subject
     * @param string $text
     * @throws Exception
     */
    public function sendText( $address, $subject, $text ) {
        $mail = $this->create($subject);
        $mail->addAddress($address);
        $mail->Body = $text;
        if ( !$mail->send() ) {
            throw new \Exception($mail->ErrorInfo);
        }
    }
}