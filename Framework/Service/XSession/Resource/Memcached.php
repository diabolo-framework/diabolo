<?php 
class Memcached {
    const RES_NOTFOUND = 'EXAMPLE';
    
    public function __construct ( $persistent_id  ) {}
    public function add ( $key , $value , $expiration  ) {}
    public function addByKey ( $server_key , $key , $value , $expiration  ) {}
    public function addServer ( $host , $port , $weight = 0  ) {}
    public function addServers ( $servers ) {}
    public function append ( $key , $value ) {}
    public function appendByKey ( $server_key , $key , $value ) {}
    public function cas ( $cas_token , $key , $value , $expiration  ) {}
    public function casByKey ( $cas_token , $server_key , $key , $value , $expiration  ) {}
    public function decrement ( $key , $offset = 1  ) {}
    public function decrementByKey ( $server_key , $key , $offset = 1 , $initial_value = 0 , $expiry = 0  ) {}
    public function delete ( $key , $time = 0  ) {}
    public function deleteByKey ( $server_key , $key , $time = 0  ) {}
    public function deleteMulti ( $keys , $time = 0  ) {}
    public function deleteMultiByKey ( $server_key , $keys , $time = 0  ) {}
    public function fetch ( ) {}
    public function fetchAll ( ) {}
    public function flush ( $delay = 0  ) {}
    public function get ( $key ,  $cache_cb , &$cas_token  ) {}
    public function getAllKeys ( ) {}
    public function getByKey ( $server_key , $key ,  $cache_cb , &$cas_token  ) {}
    public function getDelayed ( $keys , $with_cas ,  $value_cb  ) {}
    public function getDelayedByKey ( $server_key , $keys , $with_cas ,  $value_cb  ) {}
    public function getMulti ( $keys , &$cas_tokens , $flags  ) {}
    public function getMultiByKey ( $server_key , $keys , &$cas_tokens , $flags  ) {}
    public function getOption ( $option ) {}
    public function getResultCode ( ) {}
    public function getResultMessage ( ) {}
    public function getServerByKey ( $server_key ) {}
    public function getServerList ( ) {}
    public function getStats ( ) {}
    public function getVersion ( ) {}
    public function increment ( $key , $offset = 1  ) {}
    public function incrementByKey ( $server_key , $key , $offset = 1 , $initial_value = 0 , $expiry = 0  ) {}
    public function isPersistent ( ) {}
    public function isPristine ( ) {}
    public function prepend ( $key , $value ) {}
    public function prependByKey ( $server_key , $key , $value ) {}
    public function quit ( ) {}
    public function replace ( $key , $value , $expiration  ) {}
    public function replaceByKey ( $server_key , $key , $value , $expiration  ) {}
    public function resetServerList ( ) {}
    public function set ( $key , $value , $expiration  ) {}
    public function setByKey ( $server_key , $key , $value , $expiration  ) {}
    public function setMulti ( $items , $expiration  ) {}
    public function setMultiByKey ( $server_key , $items , $expiration  ) {}
    public function setOption ( $option , $value ) {}
    public function setOptions ( $options ) {}
    public function setSaslAuthData ( $username , $password ) {}
    public function touch ( $key , $expiration ) {}
    public function touchByKey ( $server_key , $key , $expiration ) {}
}