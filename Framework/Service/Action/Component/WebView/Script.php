<?php
namespace X\Service\Action\Component\WebView;
/**
 * HTML 视图脚本
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Script {
    /**
     * 该脚本所属的管理器
     * @var ScriptManager
     */
    private $manager = null;
    
    /**
     * 初始化脚本实例
     * @param ScriptManager $view
     */
    public function __construct( $manager ) {
        $this->manager = $manager;
    }
    
    /**
     * 脚本类型
     * @var string
     */
    private $type = 'text/javascript';
    
    /**
     * 设置脚本类型
     * @param string $type
     * @return self
     */
    public function setType( $type ) {
        $this->type = $type;
        return $this;
    }
    
    /**
     * 脚本链接
     * @var string
     */
    private $src = null;
    
    /**
     * 设置脚本链接
     * @param string $path 链接
     * @return \X\Service\XView\Core\Util\HtmlView\Script
     */
    public function setSource( $path ) {
        $this->src = $path;
        return $this;
    }
   
    /**
     * 脚本内容
     * @var string
     */
    private $content = null;
    
    /**
     * 设置脚本内容
     * @param string $content
     * @return self
     */
    public function setContent( $content ) {
        $this->content = $content;
        return $this;
    }
    
    /**
     * 是否延迟执行
     * @var boolean
     */
    private $defer = false;
    
    /**
     * 设置是否延迟执行
     * @return self
     */
    public function enableDefer() {
        $this->defer = true;
        return $this;
    }
    
    /**
     * 脚本字符集
     * @var string
     */
    private $charset = 'UTF-8';
    
    /**
     * 设置脚本字符集
     * @param string $charset
     * @return self
     */
    public function setCharset( $charset ) {
        $this->charset = $charset;
        return $this;
    }
    
    /**
     * s是否同步执行
     * @var boolean
     */
    private $asyns = false;
    
    /**
     * 设置是否同步执行
     * @return self
     */
    private function enableAsyns() {
        $this->asyns = true;
        return $this;
    }
    
    /**
     * 以来关系
     * @var array
     */
    private $requirements = array();
    
    /**
     * 增加依赖关系
     * @param string $name 脚本名称
     * @param string $... 更多依赖的脚本
     * @return self
     */
    public function setRequirements($name) {
        $this->requirements = func_get_args();
        return $this;
    }
    
    /**
     * 获取以来关系
     * @return array
     */
    public function getRequirements() {
        return $this->requirements;
    }
    
    /**
     * 将脚本实例转换为HTMLscript标签
     * @return string
     */
    public function toString() {
        if ( null !== $this->src ) {
            return '<script type="'.$this->type.'" src="'.$this->src.'" charset="'.$this->charset.'"></script>';
        } else if ( null !== $this->content ) {
            return "<script type=\"{$this->type}\">\n{$this->content}\n</script>";
        } else {
            return null;
        }
    }
}