<?php
namespace X\Service\Router;
use X\Core\X;
use X\Core\Service\XService;
use X\Service\Router\Router\RouterBase;
/** */
class Service extends XService {
    /** @var array router config */
    protected $roters = array( );
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        if ( 'cli' !== X::system()->getEnvironment()->getName() ) {
            $this->routeCurrentRequest();
        }
    }
    
    /**
     * @param unknown $url
     * @throws RouterException
     * @return array|boolean
     */
    public function routeUrl( $url )  {
        foreach ( $this->roters as $router ) {
            $routerClasss = $router['class'];
            if ( !is_subclass_of($routerClasss, RouterBase::class) ) {
                throw new RouterException('router class `'.$router['class'].'is not subclass of '.RouterBase::class);
            }
            
            /** @var $routerInstance RouterBase */
            $routerInstance = new $routerClasss($router);
            $params = $routerInstance->router($this->getCurrentUrl());
            if ( false !== $params ) {
                return $params;
            }
        }
        return false;
    }
    
    /** */
    private function routeCurrentRequest() {
        $params = $this->routeUrl($this->getCurrentUrl());
        if ( false !== $params ) {
            X::system()->getParameter()->merge($params);
        }
    }
    
    /** @return unknown */
    private function getCurrentUrl() {
        return $_REQUEST['URI'];
    }
}