<?php
namespace X\Service\XSession;
use X\Core\Service\XService;

class Service extends XService {
    /***/
    const FLASH_SESSION_KEY = '__FLASHES__';
    
    /**
     * 服务名称
     * @var string
     */
    protected static $serviceName = 'XSession';
    
    /** 会话状态 */
    private $sessionStatus = PHP_SESSION_NONE;
    /**  @var \SessionHandlerInterface */
    private $storage = null;
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        if ( $this->getConfiguration()->get('autoStart', true) ) {
            $this->startSession();
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::stop()
     */
    public function stop () {
        if ( null !== $this->storage ) {
            $this->storage->beforeServiceClose();
        }
        return parent::stop();
    }
    
    /**
     * 获取Session中的值
     * @param unknown $name
     */
    public function get($name) {
        return $_SESSION[$name];
    }
    
    /**
     * 设置SESSION中的值
     * @param unknown $name
     * @param unknown $value
     */
    public function set($name, $value) {
        $_SESSION[$name] = $value;
    }
    
    /**
     * 增加flash
     * @param unknown $name
     * @param unknown $content
     * @throws \Exception
     */
    public function flashAdd($name, $content) {
        if ( PHP_SESSION_ACTIVE !== $this->sessionStatus ) {
            throw new \Exception('session has not been started');
        }
        if ( isset($_SESSION[self::FLASH_SESSION_KEY]) ) {
            $_SESSION[self::FLASH_SESSION_KEY] = array();
        }
        $_SESSION[self::FLASH_SESSION_KEY][$name] = $content;
        
        $this->hasChanged = true;
    }
    
    /**
     * 检查flash是否存在
     * @param unknown $name
     * @return boolean
     */
    public function flashHas($name) {
        return isset($_SESSION[self::FLASH_SESSION_KEY]) 
        && isset($_SESSION[self::FLASH_SESSION_KEY][$name]);
    }
    
    /**
     * 获取flash内容
     * @param unknown $name
     * @throws \Exception
     * @return unknown
     */
    public function flashGet($name) {
        if ( !$this->flashHas($name) ) {
            throw new \Exception("flash `{$name}` does not exists.");
        }
        
        $content = $_SESSION[self::FLASH_SESSION_KEY][$name];
        unset($_SESSION[self::FLASH_SESSION_KEY][$name]);
        return $content;
    }
    
    /**
     * 启动会话
     * @return void
     */
    public function startSession() {
        $this->setupSessionId();
        $this->setupStorage();
        
        $conf = $this->getConfiguration();
        $sessionName = $conf->get('name', 'PHPSESSID');

        $options = array();
        $options['name'] = $sessionName;
        $cookie = $conf->get('cookie', null);
        if ( null !== $cookie ) {
            $options['cookie_lifetime'] = isset($cookie['lifetime']) ? $cookie['lifetime'] : 0;
            $options['cookie_path'] = isset($cookie['path']) ? $cookie['path'] : '/';
            $options['cookie_domain'] = isset($cookie['domain']) ? $cookie['domain'] : '';
            $options['cookie_secure'] = isset($cookie['secure']) ? $cookie['secure'] : '';
            $options['cookie_httponly'] = isset($cookie['httponly']) ? $cookie['httponly'] : '';
        }
        
        session_start($options);
        $this->sessionStatus = session_status();
    }
    
    /**
     * 关闭会话
     * @param string $save
     */
    public function close( $save=ture ) {
        $save ? session_write_close() : session_abort();
    }
    
    /**
     * 销毁会话
     */
    public function destory() {
        if(isset($_COOKIE[session_name()])) {
            setCookie(session_name(),'',time()-3600,'/');
        }
        session_destroy();
    }
    
    /**
     * 垃圾回收
     */
    public function clean($lifetime=0) {
        $this->storage->gc($lifetime);
    }
    
    /**
     * 初始化存储
     */
    private function setupStorage() {
        $conf = $this->getConfiguration();
        $storage = $conf->get('storage', null);
        if ( null === $storage ) {
            return;
        }
        
        $handler = '\\X\\Service\\XSession\\Storage\\'.ucfirst($storage['type']);
        if ( !class_exists($handler) ) {
            throw new \Exception("session storage `{$storage['type']}` does not support.");
        }
        
        $stroageObj = new $handler($storage);
        $this->storage = $stroageObj;
        
        session_set_save_handler($stroageObj);
    }
    
    /**
     * 初始化SessionID
     */
    private function setupSessionId() {
        $conf = $this->getConfiguration();
        
        $sessionName = $conf->get('name', 'PHPSESSID');
        $holders = $conf->get('holders', array());
        if ( empty($holders) ) {
            throw new \Exception("session holder could not be empty");
        }
        foreach ( $holders as $sidHolder ) {
            $paramName =  '_'.strtoupper($sidHolder);
            if ( !isset($GLOBALS[$paramName]) ) {
                continue;
            }
            if ( !isset($GLOBALS[$paramName][$sessionName]) ) {
                continue;
            }
            session_id($GLOBALS[$paramName][$sessionName]);
            break;
        }
    }
}