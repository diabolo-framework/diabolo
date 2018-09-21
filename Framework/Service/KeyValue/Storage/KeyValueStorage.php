<?php
namespace X\Service\KeyValue\Storage;
interface KeyValueStorage {
    /** Key option expired at */
    const KEYOPT_EXPIRE_AT = 0;
    
    /**
     * @param string $key
     * @return mixed
     */
    function get( $key );
    
    /**
     * @param string $key
     * @param mixed $value
     * @param array $option
     * @return void
     */
    function set( $key, $value, $option=array() );
    
    /**
     * @param string $key
     * @return void
     */
    function delete( $key );
}