<?php
namespace X\Core\Module;
use X\Core\X;
use X\Core\Util\XUtil;
use X\Core\Util\ConfigurationFile;
use X\Core\Util\ConfigurationArray;
abstract class XModule {
    /** @var ConfigurationArray contains the config of this module.*/
    private $configuration = null;
    
    /**
     * @param array $parameters
     */
    abstract public function run($parameters=array());
    
    /**
     * 
     */
    public function __construct( $config=array() ) {
        $this->configuration = new ConfigurationArray();
        $this->configuration->setValues($config);
        
        $this->onLoaded();
    }
    
    /** @return self */
    public static function getModule() {
        return X::system()->getModuleManager()->get(self::getModuleName());
    }
    
    /**
     * @return boolean
     */
    protected function onLoaded() {
        return true;
    }
    
    /**
     * @return null
     */
    public function afterLoaded() {
        return null;
    }
    
    /**
     * @return string
     */
    public function getName() {
        $className = get_class($this);
        $className = explode('\\', $className);
        return $className[count($className)-2];
    }
    
    /**
     * @return string
     */
    public static function getModuleName() {
        $className = get_called_class();
        $className = explode('\\', $className);
        return $className[count($className)-2];
    }
    
    /**
     * @param string $path
     * @return string
     */
    public function getPath( $path=null ) {
        return XUtil::getPathRelatedClass($this, $path);
    }
    
    /**
     * @return \X\Core\Util\ConfigurationFile
     */
    public function getConfiguration( ) {
        return $this->configuration;
    }
    
    /**
     * @return string
     */
    public function getPrettyName() {
        return $this->getName();
    }
    
    /**
     * @return string
     */
    public function getDescription() {
        return '';
    }
    
    /**
     * @return array
     */
    public function getVersion() {
        return array(0,0,0);
    }
    
    /**
     * @return NULL
     */
    protected function onEnabled() {
        return null;
    }
    
    /**
     * @return NULL
     */
    protected function onDisabled() {
        return null;
    }
    
    /**
     * @return string
     */
    public function getAssetsPath() {
        return X::system()->getPath('Assets/'.$this->getName());
    }
    
    /**
     * @return string
     */
    public function getAssetsLink( $assets ) {
        return 'Assets/'.$this->getName().'/'.$assets;
    }
    
    /**
     * @return void
     */
    public function publishAssets() {
        $fromPath = $this->getPath('Assets/');
        if ( !is_dir($fromPath) ) {
            return;
        }
        
        $targetPath = $this->getAssetsPath();
        if ( !is_dir($targetPath) ) {
            mkdir($targetPath, 0777, true);
        }
        
        XUtil::copyPath($fromPath, $targetPath);
    }
    
    /**
     * @return void
     */
    public function cleanAssets() {
        $path = $this->getAssetsPath();
        if ( is_dir($path) ) {
            XUtil::deleteFile($path);
        }
    }
    
    /**
     * @return string
     */
    public function getAssetsLastPublishTime() {
        $path = $this->getAssetsPath();
        if ( is_dir($path) ) {
            return filemtime($path);
        }
        return null;
    }
}