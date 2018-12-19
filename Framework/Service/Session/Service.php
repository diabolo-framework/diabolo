<?php
namespace X\Service\Session;
use X\Core\Service\XService;
class Service extends XService {
    /** @var boolean */
    protected $autoStart = true;
    /** @var string */
    protected $sessionName = 'PHPSESSID';
    /** @var array */
    protected $holders = array('cookie', 'get', 'post', 'request');
    /** @var null|array */
    protected $storage = null;
    /** @var null|array */
    protected $cookie = null;
    
    /** 会话状态 */
    private $sessionStatus = PHP_SESSION_NONE;
    /**  @var \SessionHandlerInterface */
    private $storageHandler = null;
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        if ( $this->autoStart ) {
            $this->startSession();
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::stop()
     */
    public function stop() {
        session_write_close();
        parent::stop();
    }
    
    /**
     * 获取Session中的值
     * @param unknown $name
     */
    public function get($name, $default=null) {
        return array_key_exists($name, $_SESSION) 
            ? $_SESSION[$name] 
            : $default;
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
     * 启动会话
     * @return void
     */
    public function startSession() {
        $this->setupSessionId();
        $this->setupStorage();
        
        $options = array();
        $options['name'] = $this->sessionName;
        if ( null !== $this->cookie ) {
            $cookie = $this->cookie;
            $options['cookie_lifetime'] = isset($cookie['lifetime']) ? $cookie['lifetime'] : 0;
            $options['cookie_path'] = isset($cookie['path']) ? $cookie['path'] : '/';
            $options['cookie_domain'] = isset($cookie['domain']) ? $cookie['domain'] : '';
            $options['cookie_secure'] = isset($cookie['secure']) ? $cookie['secure'] : '';
            $options['cookie_httponly'] = isset($cookie['httponly']) ? $cookie['httponly'] : '';
        }
        
        session_start($options);
        setcookie(session_name(),session_id(),time()+$options['cookie_lifetime'],'/'); 
        $this->sessionStatus = session_status();
    }
    
    /**
     * 关闭会话
     * @param string $save
     */
    public function closeSession( $save=ture ) {
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
        $this->storageHandler->gc($lifetime);
    }
    
    /**
     * 初始化存储
     */
    private function setupStorage() {
        if ( null === $this->storage ) {
            return;
        }
        
        $storageClass = $this->storage['class'];
        $stroageObj = new $storageClass($this->storage);
        $this->storageHandler = $stroageObj;
        
        session_set_save_handler($stroageObj);
    }
    
    /**
     * 初始化SessionID
     */
    private function setupSessionId() {
        if ( empty($this->holders) ) {
            throw new \Exception("session holder could not be empty");
        }
        foreach ( $this->holders as $sidHolder ) {
            $paramName =  '_'.strtoupper($sidHolder);
            if ( !isset($GLOBALS[$paramName]) ) {
                continue;
            }
            if ( !isset($GLOBALS[$paramName][$this->sessionName]) ) {
                continue;
            }
            session_id($GLOBALS[$paramName][$this->sessionName]);
            break;
        }
    }
}