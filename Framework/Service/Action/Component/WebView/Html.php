<?php
namespace X\Service\Action\Component\WebView;
use X\Core\Component\ConfigurationArray;
/**
 * HTML视图
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Html {
    /** 蜘蛛索引方式 */
    const ROBOT_INDEX_ALL = 'all';
    const ROBOT_INDEX_NONE = 'none';
    const ROBOT_INDEX_NOINDEX = 'noindex';
    const ROBOT_INDEX_NOFOLLOW = 'nofollow';
    const ROBOT_INDEX_INDEX = 'index';
    const ROBOT_INDEX_FOLLOW = 'follow';
    
    /** @var StyleManager 样式管理器 */
    private $styleManager = null;
    /** @var LinkManager 链接管理器 */
    private $linkManager = null;
    /** @var MetaManager 元数据管理器 */
    private $metaManager = null;
    /** @var ScriptManager 脚本管理器 */
    private $scriptManager = null;
    /**  @var ParticleViewManager 片段管理器 */
    private $particleViewManager = null;
    /** @var ConfigurationArray 数据管理器 */
    private $dataManager = null;
    /** @var string 页面标题 */
    public $title = '';
    /** @var array 布局信息 */
    protected $layout = array(
        'view'=>null,  # 布局文件
        'content'=>null # 视图缓存渲染后的结果
    );
    
    /**
     * 初始化视图
     * @return void
     */
    public function __construct() {
        $this->styleManager = new StyleManager();
        $this->linkManager = new LinkManager($this);
        $this->metaManager = new MetaManager();
        $this->scriptManager = new ScriptManager($this);
        $this->particleViewManager = new ParticleViewManager($this);
        $this->dataManager = new ConfigurationArray();
    }
    
    /**
     * 获取样式管理器
     * @return \X\Service\XAction\Component\WebView\StyleManager
     */
    public function getStyleManager() {
        return $this->styleManager;
    }
    
    /**
     * 获取链接管理器
     * @return \X\Service\XAction\Component\WebView\LinkManager
     */
    public function getLinkManager() {
        return $this->linkManager;
    }
    
    /**
     * 获取元数据管理器
     * @return \X\Service\XAction\Component\WebView\MetaManager
     */
    public function getMetaManager() {
        return $this->metaManager;
    }
    
    /**
     * 获取脚本管理器
     * @return \X\Service\XAction\Component\WebView\ScriptManager
     */
    public function getScriptManager() {
        return $this->scriptManager;
    }
    
    /**
     * 获取视图片段管理器
     * @return \X\Service\XAction\Component\WebView\ParticleViewManager
     */
    public function getParticleViewManager() {
        return $this->particleViewManager;
    }
    
    /**
     * 获取视图全局数据管理器
     * @return \X\Core\Component\ConfigurationArray
     */
    public function getDataManager() {
        return $this->dataManager;
    }
    
    /**
     * 将视图转换为HTML字符串， 如果布局文件没有设置，则无法转换。
     * @return string
     */
    public function toString() {
        if ( null === $this->layout['view'] ) {
            return null;
        }
        
        $layoutContent = $this->renderLayout();
        
        $content = array();
        $content[] = '<!DOCTYPE html>';
        $content[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $content[] = '<head>';
        $content[] = '<title>'.htmlspecialchars($this->title).'</title>';
        $content[] = $this->getStyleManager()->toString();
        $content[] = $this->getLinkManager()->toString();
        $content[] = $this->getMetaManager()->toString();
        $content[] = $this->getScriptManager()->toString();
        $content[] = '</head>';
        $content[] = $layoutContent;
        $content[] = '</html>';
        
        $content = array_filter($content);
        $content = implode("\n", $content);
        $this->layout['content'] = $content;
        return $content;
    }
    
    /**
     * 渲染当前视图
     * @return string
     */
    private function renderLayout() {
        if ( is_string($this->layout['view']) && is_file($this->layout['view']) ) {
            extract($this->getDataManager()->toArray(), EXTR_OVERWRITE);
            ob_start();
            ob_implicit_flush(false);
            require $this->layout['view'];
            $this->layout['content'] = ob_get_clean();
        } else if ( is_callable($this->layout['view']) ) {
            $this->layout['content'] = call_user_func_array($this->layout['view'], array($this));
        } else if ( is_string($this->layout['view']) ) {
            $this->layout['content'] = $this->layout['view'];
        } else {
            $this->layout['content'] = '';
        }
        return $this->layout['content'];
    }
    
    /**
     * 渲染文件并获取渲染后的结果
     * @param string $view
     * @param array $data
     * @return string
     */
    public static function renderView( $view, $data=array() ) {
        $content = '';
        if ( is_string($view) && is_file($view) ) {
            $_view = $view;
            extract($data, EXTR_OVERWRITE);
            ob_start();
            ob_implicit_flush(false);
            require $_view;
            $content = ob_get_clean();
        } else if ( is_callable($view) ) {
            $content = call_user_func_array($view, array($data));
        } else if ( is_string($view) ) {
            $content = $view;
        } else {
            $content = '';
        }
        return $content;
    }
    
    /**
     * 设置布局处理器
     * @param string $name The name of the layout
     */
    public function setLayout( $handler ) {
        $this->layout['view'] = $handler;
    }
    
    /**
     * 输出当前视图
     * @return null
     */
    public function display() {
        echo $this->toString();
    }
    
    /**
     * 清空当前视图的渲染缓存
     * @return void
     */
    public function cleanUp() {
        $this->layout['content'] = null;
        $this->getParticleViewManager()->cleanUp();
    }
    
    /**
     * 转义字符串以用于安全的HTML文字输出
     * @param string $string 待编码的字符串
     * @return string
     */
    public static function HTMLEncode( $string ) {
        return htmlentities($string, ENT_COMPAT,'UTF-8');
    }
    
    /**
     * 转义字符串以用于安全的HTML属性文本输出
     * @param string $string 待编码的字符串
     * @return string
     */
    public static function HTMLAttributeEncode($string) {
        return htmlentities($string, ENT_COMPAT,'UTF-8');
    }
    
    /**
     * 转义字符串以用于安全的Javascript变量文本输出
     * @param string $string 待编码的字符串
     */
    public static function JavascriptValueEncode($string){
        return json_encode($string);
    }
}