<?php
namespace X\Service\XMail\Core;
use X\Service\XMail\Service as XMailService;
/**
 * 邮件类
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Mailer extends \PHPMailer {
    /**
     * 处理器名称
     * @var string
     */
    private $handler = 'default';
    
    /**
     * 设置处理器名称
     * @param string $name 处理器名称
     * @return self
     */
    public function setHandler( $name ) {
        $this->handler = $name;
        return $this;
    }
    
    /**
     * 设置邮件内容
     * @param string $content 内容
     * @return self
     */
    public function setContent( $content ) {
        $this->Body = $content;
        return $this;
    }
    
    /**
     * 发送邮件
     * @see PHPMailer::send()
     */
    public function send() {
        $handlers = XMailService::getService()->getConfiguration()->get('handlers', array());
        if ( !isset($handlers[$this->handler]) ) {
            throw new \Exception("Mail handler `{$this->handler}` does not exists.");
        }
        $this->setupByConfig($handlers[$this->handler]);
        
        return parent::send();
    }
    
    /**
     * setup current mail by configuration.
     * @param array $config
     */
    private function setupByConfig( $config ) {
        $handler = 'setupByConfigHandler'.ucfirst($config['handler']);
        $this->$handler($config);
    }
    
    /**
     * setup stmp configraion.
     * @param array $config
     */
    private function setupByConfigHandlerSmtp( $config ) {
        $this->isSMTP();
        $this->Host = $config['host'];
        $this->Port = $config['port'];
        $this->From = $config['from'];
        $this->FromName = $config['from_name'];
        $this->SMTPAuth = $config['auth_required'];
        if ( $this->SMTPAuth ) {
            $this->Username=$config['username'];
            $this->Password = $config['password'];
        }
    }
}