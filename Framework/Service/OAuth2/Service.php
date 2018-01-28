<?php
namespace X\Service\OAuth2;
use X\Core\Service\XService;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;

class Service extends XService {
    /**
     * 服务名称
     * @var string
     */
    protected static $serviceName = 'OAuth2';
    
    /**
     * OAuth 服务处理器实例 
     * @var \OAuth2\Server 
     * */
    private $server = null;
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        
        require_once(dirname(__FILE__).'/Library/OAuth2/Autoloader.php');
        \OAuth2\Autoloader::register();
        
        $config = $this->getConfiguration();
        # 存储方式
        $storageHandler = "\\OAuth2\\Storage\\{$config['storage_handler']}";
        $storage = new $storageHandler($config['storage_params']);
        $server = new \OAuth2\Server($storage, $config['option']);
        # 设置授权方式
        foreach ( $config['grant_types'] as $grantType ) {
            $grantTypeHandler = "\\OAuth2\\GrantType\\{$grantType}";
            if ( !class_exists($grantTypeHandler) ) {
                throw new \Exception("grant type `{$grantType}` does not support.");
            }
            $server->addGrantType(new $grantTypeHandler($storage));
        }
        $this->server = $server;
    }
    
    /**
     * 获取Access Token
     * @return \OAuth2\Response
     */
    public function generateAccessToken( $request=null ) {
        if ( null === $request ) {
            $request = \OAuth2\Request::createFromGlobals();
        }
        return $this->server->handleTokenRequest($request);
    }
    
    /**
     * 验证资源请求
     * @param unknown $request
     */
    public function verifyResourceRequest( $request=null ) {
        if ( null === $request ) {
            $request = \OAuth2\Request::createFromGlobals();
        }
        return $this->server->verifyResourceRequest($request);
    }
    
    
    
    
    
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @see \OAuth2\Server::validateAuthorizeRequest
     * @deprecated
     */
    public function validateAuthorizeRequest(RequestInterface $request, ResponseInterface $response = null) {
        return $this->server->validateAuthorizeRequest($request, $response);
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function __call( $name, $params=array() ) {
        return call_user_func_array(array($this->server, $name), $params);
    }
}