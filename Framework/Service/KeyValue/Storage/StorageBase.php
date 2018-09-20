<?php
namespace X\Service\KeyValue\Storage;
use X\Service\KeyValue\KeyValueException;
abstract class StorageBase {
    const KEYOPT_EXPIRE_AT = 0;
    
    /**
     * */
    public function __construct( $config ) {
        foreach ( $config as $key => $value ) {
            if ( property_exists($this, $key) ) {
                $this->$key = $value;
            }
        }
        $this->init();
    }
    
    protected function init() {
        
    }
    
    /**
     * clean all keys in storage
     * @throws KeyValueException
     * @return void
     */
    abstract public function clean();
    /**
     * use to find keys by prefix string
     * @param unknown $pattern
     * @return array
     */
    abstract public function match( $pattern );
    abstract public function rename( $key, $newKey );
    abstract public function delete( $key );
    abstract public function exists( $key );
    abstract public function size();
    abstract public function get($key);
    /**
     * set a value to key
     * @param unknown $key
     * @param unknown $value
     * @param array $option
     * @return void
     */
    abstract public function set($key, $value, $option=array());
}