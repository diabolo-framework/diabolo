<?php
namespace X\Service\Session;
class SessionFlash {
    /***/
    const FLASH_SESSION_KEY = '__FLASHES__';
    /** @var self */
    private static $flashManager = null;
    
    /** @return self */
    private static function getManager() {
        if ( null === self::$flashManager ) {
            self::$flashManager = new self();
        }
        return self::$flashManager;
    }
    
    /**
     * @param unknown $name
     * @return mixed
     */
    public static function get( $name ) {
        return self::getManager()->flashGet($name);
    }
    
    /**
     * @param unknown $name
     * @param unknown $value
     */
    public static function set( $name, $value ) {
        return self::getManager()->flashAdd($name, $value);
    }
    
    /**
     * @param unknown $name
     * @return boolean
     */
    public static function has( $name ) {
        return self::getManager()->flashHas($name);
    }
    
    /**
     * 增加flash
     * @param unknown $name
     * @param unknown $content
     * @throws \Exception
     */
    private function flashAdd($name, $content) {
        if ( PHP_SESSION_ACTIVE !== session_status() ) {
            throw new SessionException('session has not been started');
        }
        if ( isset($_SESSION[self::FLASH_SESSION_KEY]) ) {
            $_SESSION[self::FLASH_SESSION_KEY] = array();
        }
        $_SESSION[self::FLASH_SESSION_KEY][$name] = $content;
    }
    
    /**
     * 检查flash是否存在
     * @param unknown $name
     * @return boolean
     */
    private function flashHas($name) {
        return isset($_SESSION[self::FLASH_SESSION_KEY])
        && isset($_SESSION[self::FLASH_SESSION_KEY][$name]);
    }
    
    /**
     * 获取flash内容
     * @param unknown $name
     * @throws \Exception
     * @return unknown
     */
    private function flashGet($name) {
        if ( !$this->flashHas($name) ) {
            throw new SessionException("flash `{$name}` does not exists.");
        }
        
        $content = $_SESSION[self::FLASH_SESSION_KEY][$name];
        unset($_SESSION[self::FLASH_SESSION_KEY][$name]);
        return $content;
    }
}