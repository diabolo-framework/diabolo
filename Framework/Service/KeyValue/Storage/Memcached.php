<?php
namespace X\Service\KeyValue\Storage;
use X\Service\KeyValue\KeyValueException;

/**
 * @link http://php.net/manual/zh/book.memcached.php
 */
class Memcached extends StorageBase {
    /** @var string */
    protected $host = null;
    /** @var string */
    protected $port = 11211;
    /** @var array */
    protected $servers = null;
    /** @var string */
    protected $prefix = null;
    
    /** @var \Memcached */
    private $memcahced = null;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::init()
     */
    protected function init() {
        $this->memcahced = new \Memcached();
        if ( null === $this->servers ) {
            $this->memcahced->addServer($this->host, $this->port);
        } else {
            $this->memcahced->addServers($this->servers);
        }
        if ( null !== $this->prefix ) {
            $this->memcahced->setOption(\Memcached::OPT_PREFIX_KEY, $this->prefix);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::clean()
     */
    public function clean() {
        if ( false === $this->memcahced->flush() ) {
            throw new KeyValueException('failed to flush memcache');
        }
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::match()
     */
    public function match($pattern) {
        throw new KeyValueException('memcached does not support match keys');
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::rename()
     */
    public function rename($key, $newKey) {
        $value = $this->get($key);
        $this->delete($key);
        $this->set($newKey, $value);
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::delete()
     */
    public function delete($key) {
        if ( false === $this->memcahced->delete($key) ) {
            throw new KeyValueException("failed to delete key `{$key}` in storage");
        }
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::exists()
     */
    public function exists($key) {
        $this->memcahced->get($key);
        return \Memcached::RES_NOTFOUND !== $this->memcahced->getResultCode();
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::size()
     */
    public function size() {
        throw new KeyValueException('unable to get right size of item right now');
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::get()
     */
    public function get($key) {
        $value = $this->memcahced->get($key);
        if ( \Memcached::RES_NOTFOUND === $this->memcahced->getResultCode() ) {
            return null;
        }
        return $value;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::set()
     */
    public function set($key, $value, $option = array()) {
        if ( isset($option[self::KEYOPT_EXPIRE_AT]) ) {
            $this->memcahced->set($key, $value, $option[self::KEYOPT_EXPIRE_AT]);
        } else {
            $this->memcahced->set($key, $value);
        }
    }
}