<?php
namespace X\Core\Component;
abstract class OptionalObject {
    /**
     * construct object and set prooerties.
     * @param array $options
     */
    public function __construct( $options ) {
        foreach ( $options as $key => $value ) {
            $this->$key = $value;
        }
        
        $this->init();
    }
    
    /**
     * init object after all properties were set.
     * @return void
     */
    protected function init() {}
}