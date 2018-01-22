<?php
namespace X\Service\XError\Processor;
use X\Service\XError\Service as XErrorService;
/**
 * error processor base class.
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class Processor {
    /**
     * The current x-error service.
     * @var XErrorService
     */
    private $servie = null;
    
    /**
     * the configuration of current processor.
     * @var array
     */
    private $config = array();
    
    /**
     * Init current processor
     * @param XErrorService $service
     * @param array $config
     */
    public function __construct( $service, $config ) {
        $this->servie = $service;
        $this->config = $config;
    }
    
    /**
     * get x-error service.
     * @return \X\Service\XError\Service
     */
    public function getService() {
        return $this->servie;
    }
    
    /**
     * get configuration of current error processor.
     * @return array
     */
    public function getConfiguration() {
        return $this->config;
    }
    
    /**
     * process error.
     * @return void
     */
    abstract public function process();
}