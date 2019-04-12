<?php
namespace X\Core\Object;
class Singleton {
    /**
     * @var self[]
     */
    private static $instances = array();
    
    /**
     * @return self
     */
    public static function getInstance( ) {
        $class = get_called_class();
        if ( !isset(self::$instances[$class]) ) {
            self::$instances[$class] = new static();
        }
        return self::$instances[$class];
    }
    
    /**
     * @param string $options
     * @throws \Exception
     * @return self
     */
    public static function setup( $options ) {
        $class = get_called_class();
        if ( isset(self::$instances[$class]) ) {
            throw new \Exception("{$class} has been setup");
        }
        self::$instances[$class] = new static($options);
        return self::$instances[$class];
    }
    
    /**
     * @param array $options
     */
    protected function __construct( $options = array () ) {
        foreach ( $options as $key => $value ) {
            if ( property_exists($this, $key) ) {
                $this->{$key} = $value;
            }
        }
        $this->init();
    }
    
    /**
     * 
     */
    protected function init() {
        
    }
}