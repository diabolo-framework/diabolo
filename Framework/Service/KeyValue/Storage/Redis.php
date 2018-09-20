<?php
namespace X\Service\KeyValue\Storage;
use X\Service\KeyValue\KeyValueException;

/**
 * @link https://github.com/phpredis/phpredis#class-redis
 */
class Redis extends StorageBase {
    /** @var string */
    protected $host = null;
    /** @var integer */
    protected $port = 6379;
    /** @var string */
    protected $password = null;
    /** @var integer */
    protected $dbindex = null;
    /** @var string */
    protected $prefix = null;
    
    /** @var \Redis */
    private $redis = null;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::init()
     */
    protected function init() {
        $redis = new \Redis();
        if ( !$redis->connect($this->host, $this->port) ) {
            throw new KeyValueException("failed to connect to redis server {$this->host}:{$this->port}");
        }
        if ( null !== $this->password && false === $redis->auth($this->password)) {
            throw new KeyValueException('redis service auth failed');
        }
        if ( null !== $this->dbindex ) {
            $redis->select($this->dbindex);
        }
        if ( null !== $this->prefix ) {
            $redis->setOption(\Redis::OPT_PREFIX, $this->prefix);
        }
        $this->redis = $redis;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::get()
     */
    public function get($key) {
        $value = $this->redis->get($key);
        if ( false === $value ) {
            return null;
        }
        return $value;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::set()
     */
    public function set($key, $value, $option = array()) {
        if ( true !== $this->redis->set($key, $value) ) {
            throw new KeyValueException("failed to set value to {$key}");
        }
        
        if ( isset($option[self::KEYOPT_EXPIRE_AT]) ) {
            $this->redis->expireAt($key, time() + $option[self::KEYOPT_EXPIRE_AT]);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::size()
     */
    public function size( ) {
        return $this->redis->dbSize();
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::delete()
     */
    public function delete( $key ) {
        return $this->redis->delete($key);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::exists()
     */
    public function exists( $key ) {
        return $this->redis->exists($key);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::rename()
     */
    public function rename( $key, $newKey ) {
        $this->redis->rename($key, $newKey);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::match()
     */
    public function match( $pattern ) {
        $keys = $this->redis->keys($pattern.'*');
        return $keys;
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::clean()
     */
    public function clean() {
        $this->redis->flushDB();
    }
    
    /** */
    public function __destruct() {
        $this->redis->close();
    }
}