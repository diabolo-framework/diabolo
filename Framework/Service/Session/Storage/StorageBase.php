<?php
namespace X\Service\Session\Storage;
abstract class StorageBase implements \SessionHandlerInterface {
    public function __construct( $option ) {
        foreach ( $option as $key => $value ) {
            if ( property_exists($this, $key) ) {
                $this->$key = $value;
            }
        }
        $this->init();
    }
    
    protected function init() {}
}