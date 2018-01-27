<?php
namespace X\Service\XSession\Storage;
class Redis implements \SessionHandlerInterface {
    /** 配置项目 */
    private $option = null;
    /** @var \Redis */
    private $redis = null;
    /** 旧的会话值 */
    private $oldSessionValue = null;
    
    /**
     * @param unknown $option
     */
    public function __construct( $option ) {
        $this->option = $option;
    }
    
    /**
     * @return boolean
     */
    public function beforeServiceClose() {
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::open()
     */
    public function open($save_path, $session_name) {
        $redis = new \Redis();
        $redis->connect($this->option['host'], $this->option['port']);
        if ( !empty($this->option['password']) ) {
            $redis->auth($this->option['password']);
        }
        $redis->select($this->option['database']);
        $this->redis = $redis;
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::close()
     */
    public function close() {
        $this->redis->close();
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::read()
     */
    public function read($session_id) {
        $key = $this->option['prefix'].$session_id;
        $value = $this->redis->get($key);
        $this->oldSessionValue = $value;
        return (false===$value) ? '' : $value;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::write()
     */
    public function write($session_id, $session_data) {
        $key = $this->option['prefix'].$session_id;
        if ( $session_data === $this->oldSessionValue ) {
            $this->redis->setTimeout($key, $this->option['lifetime']);
            return true;
        }
        
        $this->redis->set($key, $session_data);
        $this->redis->setTimeout($key, $this->option['lifetime']);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::destroy()
     */
    public function destroy($session_id) {
        $key = $this->option['prefix'].$session_id;
        $this->redis->delete($key);
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