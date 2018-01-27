<?php
namespace X\Service\XSession\Storage;
class Memcached implements \SessionHandlerInterface {
    /** @var array */
    private $option = null;
    /***/
    private $memcached = null;
    
    /**
     * @param unknown $option
     */
    public function __construct( $option ) {
        $this->option = $option;
    }
    
    /**
     * 在服务关闭之前调用
     * @return void
     */
    public function beforeServiceClose() {
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::open()
     */
    public function open($save_path, $session_name) {
        $memcached = new \Memcached();
        $memcached->addServer($this->option['host'], $this->option['port']);
        $this->memcached = $memcached;
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::close()
     */
    public function close() {
        $this->memcached->quit();
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::read()
     */
    public function read($session_id) {
        $key = $this->option['prefix'].$session_id;
        $value = $this->memcached->get($key);
        
        if ( \Memcached::RES_NOTFOUND === $this->memcached->getResultCode() ) {
            $value = '';
        }
        return $value;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::write()
     */
    public function write($session_id, $session_data) {
        $key = $this->option['prefix'].$session_id;
        $this->memcached->set($key, $session_data, $this->option['lifetime']);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::destroy()
     */
    public function destroy($session_id) {
        $key = $this->option['prefix'].$session_id;
        $this->memcached->delete($key);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::gc()
     */
    public function gc($maxlifetime) {
        return true;
    }
}