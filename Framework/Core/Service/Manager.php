<?php
namespace X\Core\Service;
use X\Core\X;
use X\Core\Component\Exception;
use X\Core\Component\Manager as UtilManager;
class Manager extends UtilManager {
    /** @var string */
    protected $configurationKey = 'services';
    /** @var array hood all services instance and status. */
    private $services = array(
        # 'name' => array(
        #      'isLoaded'  => true,
        #      'service'   => $service)
    );
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Component\Manager::start()
     */
    public function start(){
        parent::start();
        foreach ( $this->getConfiguration() as $name => $configuration ) {
            if ( $configuration['enable'] ) {
                $this->load($name);
                if ( isset($configuration['delay']) && false === $configuration['delay'] ) {
                    $this->get($name)->start();
                }
            } else {
                $this->services[$name]['isLoaded']  = false;
                $this->services[$name]['service']   = null;
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \X\Core\Component\Manager::stop()
     */
    public function stop() {
        foreach ( $this->services as $name => $service ) {
            if( null === $service['service'] ) {
                continue;
            }
            if ( XService::STATUS_RUNNING === $service['service']->getStatus() ) {
                $service['service']->stop();
                $service['service']->destroy();
            }
        }
        $this->services = array();
        parent::stop();
    }
    
    /**
     * @param string $name
     * @throws Exception
     */
    public function load($name) {
        if ( !$this->has($name) ) {
            throw new Exception("Service '$name' does not exists.");
        }
        
        $configuration = $this->getConfiguration()->get($name);
        $serviceClass = $configuration['class'];
        if ( !class_exists($serviceClass, true) ) {
            throw new Exception("Service class '$name' does not exists.");
        }
        
        if ( !( is_subclass_of($serviceClass, '\\X\\Core\\Service\\XService') ) ) {
            throw new Exception("Service class '$serviceClass' should be extends from '\\X\\Core\\Service\\XService'.");
        }
        
        $service = $serviceClass::getService($configuration['params']);
        $this->services[$name]['isLoaded']  = true;
        $this->services[$name]['service']   = $service;
    }
    
    /**
     * @param unknown $serviceName
     * @throws Exception
     * @return boolean
     */
    public function isLoaded( $serviceName ) {
        if ( !$this->has($serviceName) ) {
            throw new Exception("Service '$serviceName' does not exists.");
        }
        return isset($this->services[$serviceName]) ? $this->services[$serviceName]['isLoaded'] : false;
    }
    
    /**
     * @param string $name
     * @throws Exception
     */
    public function unload( $name ) {
        if ( !$this->has($name) ) {
            throw new Exception("Service '$name' does not exists.");
        }
        
        if ( !$this->isLoaded($name) ) {
            return;
        }
        
        $this->services[$name]['service'] = null;
        $this->services[$name]['isLoaded'] = false;
    }
    
    /**
     * @param string $name
     * @throws Exception
     * @return \X\Core\Service\XService
     */
    public function get( $name ) {
        if ( !$this->has($name) ) {
            throw new Exception("Service '$name' does not exists.");
        }
        
        if ( !$this->isLoaded($name) ) {
            $this->load($name, $this->configuration[$name]);
        }
        
        /* @var $service \X\Core\Service\XService */
        $service = $this->services[$name]['service'];
        if ( $this->isEnabled($name) && $this->isLazyLoadEnabled($name) && XService::STATUS_STOPPED===$service->getStatus() ) {
            $service->start();
        }
        return $service;
    }
    
    /** @return boolean */
    public function isEnabled( $name ) {
        $config = $this->getConfiguration()->get($name);
        return isset($config['enable']) ? $config['enable'] : false;
    }
    
    /** @return boolean */
    public function isLazyLoadEnabled($name) {
        $config = $this->getConfiguration()->get($name);
        return isset($config['delay']) ? $config['delay'] : false;
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function has( $name ) {
        return $this->getConfiguration()->has($name);
    }
    
    /**
     * @return array
     */
    public function getList() {
        return array_keys($this->getConfiguration()->toArray());
    }
}