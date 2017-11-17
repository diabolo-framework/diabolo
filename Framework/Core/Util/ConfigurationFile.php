<?php
/**
 *
 */
namespace X\Core\Util;

/**
 * 
 */
class ConfigurationFile extends ConfigurationArray  {
    /**
     * 该变量用来保存该配置文件的存储位置。
     * @var string
     */
    protected $path = null;
    
    /**
     * 初始化该配置类
     * @param string $path 配置文件的存储位置。
     */
    public function __construct( $path ) {
        if ( !file_exists($path) ) {
            $this->merge(array());
        } else if (!is_file($path)) {
            throw new Exception('Configuration file "'.$path.'" is not a regular file.');
        } else {
            $configuration = require $path;
            if ( !is_array($configuration) ) {
                throw new Exception('Invalid configuration file :"'.$path.'".');
            }
            $this->merge($configuration);
        }
        $this->path = $path;
    }
    
    /**
     * 保存配置信息。如果没有更改则不会进行保存。
     * @return void
     */
    public function save() {
        XUtil::storeArrayToPHPFile($this->path, $this->toArray());
    }
}