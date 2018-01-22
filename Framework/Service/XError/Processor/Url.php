<?php
namespace X\Service\XError\Processor;
use X\Core\X;
/**
 * go to another page to display error information.
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Url extends Processor {
    /**
     * (non-PHPdoc)
     * @see \X\Service\XError\Util\Processor::process()
     */
    public function process() {
        $params = array();
        $error = $this->getService()->getErrorInformation();
        $config = $this->getConfiguration();
        
        if ( 'cli' === X::system()->getEnvironment()->getName() && $config['gotoUrl']) {
            $this->getService()->throwAnotherException('Unable to jump to url on cli mode.');
            return;
        }
        
        foreach ( $config['parameters'] as $name => $value ) {
            $params[$name]=array_key_exists($name, $error) ? $error[$name] : $value;
        }
        
        if ( $config['gotoUrl'] ) { # 渲染页面并跳转
            $this->handleRequestWithJump($config, $params);
        } else { # 使用CURL调用
            $this->handleRequestWithoutJump($config, $params);
        }
    }
    
    /**
     * 跳转的处理
     * @param unknown $config
     */
    private function handleRequestWithJump( $config, $params ) {
        $url = $config['url'];
        
        if ( 'get' === strtolower($config['method']) && !empty($params)) {
            $params = http_build_query($params);
            $connector = (false===strpos('?', $url)) ? '?' : '&';
            $url = $url.$connector.$params;
        }
        
        $path = $this->getService()->getPath('View/UrlJump.php');
        require $path;
    }
    
    /**
     * 不跳转的处理
     * @param unknown $config
     */
    private function handleRequestWithoutJump($config, $params) {
        $url = $config['url'];
        
        $ch = curl_init();
        if ( 'get' === strtolower($config['method']) ) {
            $params = http_build_query($params);
            $connector = (false===strpos('?', $url)) ? '?' : '&';
            $url = $url.$connector.$params;
        } else if ('post' === strtolower($config['method']) ) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            $this->getService()->throwAnotherException("request method `{$config['method']}` does not support.");
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch);
        if ( 0 !== curl_errno($ch) ) {
            $this->getService()->throwAnotherException("curl error : ".curl_errno($ch));
        }
        curl_close($ch);
    }
}