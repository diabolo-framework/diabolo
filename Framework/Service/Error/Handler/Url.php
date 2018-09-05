<?php
namespace X\Service\Error\Handler;
use X\Core\X;
use X\Service\Error\Service as ErrorService;
use X\Service\Error\ErrorException;
/**
 * go to another page to display error information.
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Url extends ErrorHandler {
    /** @var string */
    protected $url = null;
    /** @var string */
    protected $method = 'get';
    /** @var array */
    protected $parameters = array();
    /** @var boolean */
    protected $gotoUrl = false;
    
    /**
     * (non-PHPdoc)
     * @see \X\Service\XError\Util\Processor::process()
     */
    public function process() {
        $params = array();
        $error = $this->getError();
        
        if ( 'cli' === X::system()->getEnvironment()->getName() && $this->gotoUrl ) {
            throw new ErrorException('Unable to jump to url on cli mode.');
        }
        
        foreach ( $this->parameters as $name => $value ) {
            $getter = 'get'.ucfirst($name);
            if ( is_callable(array($error, $getter)) ) {
                $params[$name] = $error->$getter();
            } else {
                $params[$name] = $value;
            }
        }
        
        if ( $this->gotoUrl ) { # 渲染页面并跳转
            $this->handleRequestWithJump($params);
        } else { # 使用CURL调用
            $this->handleRequestWithoutJump($params);
        }
    }
    
    /**
     * 跳转的处理
     */
    private function handleRequestWithJump( $params ) {
        $url = $this->url;
        if ( 'get' === strtolower($this->method) && !empty($params)) {
            $params = http_build_query($params);
            $connector = (false===strpos('?', $url)) ? '?' : '&';
            $url = $url.$connector.$params;
        }
        
        require ErrorService::getService()->getPath('View/UrlJump.php');
    }
    
    /**
     * 不跳转的处理
     * @param unknown $config
     */
    private function handleRequestWithoutJump($params) {
        $url = $this->url;
        
        $ch = curl_init();
        if ( 'get' === strtolower($this->method) ) {
            $params = http_build_query($params);
            $connector = (false===strpos('?', $url)) ? '?' : '&';
            $url = $url.$connector.$params;
        } else if ('post' === strtolower($this->method) ) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            throw new ErrorException("request method `{$this->method}` does not support.");
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        $response = curl_exec($ch);
        if ( 0 !== curl_errno($ch) ) {
            throw new ErrorException("curl error : ".curl_errno($ch));
        }
        curl_close($ch);
    }
}