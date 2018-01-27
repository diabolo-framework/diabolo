<?php
namespace X\Service\XAction\Component\WebView;
/**
 * 视图脚本管理器
 * @author Michael Luthor <michaelluthor@163.com>
 */
class ScriptManager {
    /**
     * 所属视图实例
     * @var \X\Service\XAction\Component\WebView\Html
     */
    private $view = null;
    
    /**
     * 初始化管理器
     * @param \X\Service\XAction\Component\WebView\Html $view 所属视图
     * @return void
     */
    public function __construct( Html $view ) {
        $this->view = $view;
    }
    
    /**
     * 获取所属视图
     * @return \X\Service\XAction\Component\WebView\Html
     */
    public function getPageView() {
        return $this->view;
    }
    
    /**
     * 脚本列表
     * @var Script[]
     */
    protected $scripts = array();
    
    /**
     * 增加脚本到视图
     * @param string $name 脚本名称
     * @param string $source 路径,默认为空
     * @return \X\Service\XAction\Component\WebView\Script
     */
    public function add( $name, $source=null ) {
        $script = new Script($this);
        if ( null !== $source ) {
            $script->setSource($source);
        }
        $this->scripts[$name] = $script;
        return $script;
    }
    
    /**
     * 增加JS变量到视图
     * @param string $name 变量名称
     * @param mixed $value 变量值
     * @return \X\Service\XView\Core\Util\HtmlView\Script
     */
    public function setValue( $name, $value ) {
        $script = new Script($this);
        $this->scripts[$name] = $script;
        $script->setContent(sprintf('var %s=%s;', $name, json_encode($value)));
        return $script;
    }
    
    /**
     * 获取脚本列表
     * @return array
     */
    public function getList() {
        return array_keys($this->scripts);
    }
    
    /**
     * 获取指定名称的脚本是否存在
     * @param string $name 脚本名称
     * @return boolean
     */
    public function has( $name ) {
        return isset($this->scripts[$name]);
    }
    
    /**
     * 获取脚本信息
     * @param string $name 脚本名称
     * @return array
     */
    public function get( $name ) {
        if ( !$this->has($name) ) {
            throw new \Exception('Can not find script "'.$name.'".');
        }
        return $this->scripts[$name];
    }
    
    /**
     * 移除脚本
     * @param string $name 脚本名称
     * @return void
     */
    public function remove($name) {
        if ( isset($this->scripts[$name]) ) {
            unset($this->scripts[$name]);
        }
    }
    
    /**
     * 内嵌脚本列表
     * @var array
     */
    private $scriptStrings = array();
    
    /**
     * 转换脚本列表为HTML代码
     * @return string
     */
    public function toString() {
        $this->scriptStrings = array();
        foreach ( $this->scripts as $name => $script ) {
            $this->renderScript($name);
        }
        if ( empty($this->scriptStrings) ) {
            return null;
        }
        $scriptList = implode("\n", $this->scriptStrings);
        return $scriptList;
    }
    
    /**
     * 转换指定的脚本为HTML代码
     * @param string $name 脚本名称
     * @return string
     */
    private function renderScript( $name ) {
        if ( isset($this->scriptStrings[$name]) ) {
            return;
        }
        
        if ( !isset($this->scripts[$name]) ) {
            throw new \Exception('Script "'.$name.'" does not exists.');
        }
        
        $script = $this->scripts[$name];
        $requirements = $script->getRequirements();
        foreach ( $requirements as $requirement ) {
            $this->renderScript($requirement);
        }
        
        $this->scriptStrings[$name] = $script->toString();
    }
}