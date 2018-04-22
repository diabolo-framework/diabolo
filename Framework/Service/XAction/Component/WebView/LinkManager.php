<?php
namespace X\Service\XAction\Component\WebView;
/**
 * 链接管理器
 * @author Michael Luthor <michaelluthor@163.com>
 */
class LinkManager {
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
     * 链接列表
     * @var array
     */
    protected  $links = array(
    /* 'identifier' => array(
     *      'rel'=>'stylesheet',
     *      'href'=>'style.css',
     *      'type'=>'text/css',
     *      'hreflang'=>'en',
     *      'media'=>'print',
     *      'sizes'=>'16x16'
     * ),
     */
    );
    
    /**
     * 增加CSS样式表到当前视图
     * @param string $identifier 样式表名称
     * @param string $link 样式表链接
     * @return void
     */
    public function addCSS( $identifier, $link ) {
        $this->addLink($identifier, 'stylesheet', 'text/css', $link);
    }
    
    /**
     * 设置Favicon
     * @param string $path Favicon链接地址
     * @return void
     */
    public function setFavicon( $path='/favicon.ico' ) {
        $this->addLink('favicon', 'icon', 'image/x-icon', $path);
    }
    
    /**
     * 页面资源预加载
     * @param string $url
     */
    public function addPrefetch ( $url ) {
        $this->addLink('prefetch-'.$url,'prefetch',null,$url);
    }
    
    /**
     * 增加自定义链接到当前视图
     * @param string $identifier 名称
     * @param string $rel 规定当前文档与被链接文档之间的关系。
     * @param string $type 规定被链接文档的 MIME 类型。
     * @param string $href 规定被链接文档的位置。
     * @param string $media 规定被链接文档将被显示在什么设备上。
     * @param string $hreflang 规定被链接文档中文本的语言。
     * @param string $sizes 规定被链接资源的尺寸。仅适用于 rel="icon"。
     * @link http://www.w3school.com.cn/tags/tag_link.asp
     * @return void
     */
    public function addLink( $identifier, $rel=null, $type=null, $href=null, $media=null, $hreflang=null, $sizes=null ) {
        $attributes = array(
            'rel'       => $rel,
            'href'      => $href,
            'type'      => $type,
            'hreflang'  => $hreflang,
            'media'     => $media,
            'sizes'     => $sizes
        );
        
        if ( !isset($this->links[$identifier]) ) {
            $this->links[$identifier] = $attributes;
        } else {
            $this->links[$identifier] = array_merge($this->links[$identifier],$attributes);
        }
    }
    
    /**
     * 删除指定链接
     * @param string $name 链接名称
     * @return void
     */
    public function remove( $name ) {
        if ( $this->has($name) ) {
            unset($this->links[$name]);
        }
    }
    
    /**
     * 获取当前视图是否包含指定链接
     * @param string $name 链接名称
     * @return boolean
     */
    public function has( $name ) {
        return isset($this->links[$name]);
    }
    
    /**
     * 根据名称获取链接信息
     * @param string $name 链接名称
     * @return array
     */
    public function get( $name ) {
        if ( !$this->has($name) ) {
            throw new \Exception('Link "'.$name.'" does not exists.');
        }
        return $this->links[$name];
    }
    
    /**
     * 获取当前视图所有链接信息
     * @return array
     */
    public function getList() {
        return array_keys($this->links);
    }
    
    /**
     * 将该管理器中所有的链接信息转换为HTML视图中的link标签字符串 
     * @return string
     */
    public function toString() {
        $linkList = array();
        foreach ( $this->links as $name => $link ) {
            $linkString = array('<link');
            foreach ( $link as $attr => $value ) {
                if ( null === $value ) {
                    continue;
                }
                $linkString[] = $attr.'="'.$value.'"';
            }
            $linkString[] = '/>';
            $linkList[] = implode(' ', $linkString);
        }
        
        if ( 0 === count($linkList) ) {
            return null;
        }
        
        $linkList = implode("\n", $linkList);
        return $linkList;
    }
}