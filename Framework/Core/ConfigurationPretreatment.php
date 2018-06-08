<?php
namespace X\Core;
class ConfigurationPretreatment {
    /** @var array */
    private $configuration = array();
    
    /**
     * @param array $config
     * @return void
     */
    public function __construct( $config ) {
        $this->configuration = $config;
        $this->setupModules();
    }
    
    /**
     * @return void
     */
    private function setupModules() {
        if ( !isset($this->configuration['modules']['Syscmd']) ) {
            $this->configuration['modules']['Syscmd'] = array(
                'enable'=>true,'default'=>false,'params'=>array(),
            );
        }
    }
    
    /**
     * @return array
     */
    public function getConfiguration() {
        return $this->configuration;
    }
}