<?php 
namespace X\Core\Object;
class Optional {
    /**
     * @param array $options
     */
    public function __construct( $options = array () ) {
        foreach ( $options as $key => $value ) {
            if ( property_exists($this, $key) ) {
                $this->{$key} = $value;
            }
        }
        $this->init();
    }
    
    /**
     *
     */
    protected function init() {
        
    }
}