<?php
namespace X\Service\XAction\Component\WebView;
/**
 * 视图片段管理器
 * @author Michael Luthor <michaelluthor@163.com>
 *
 */
class ParticleViewManager {
    /**
     * 片段列表
     * @var array
     */
    private $particles = array();
    
    /**
     * 所属父级对象
     * @var Html|ParticleView
     */
    private $parent = null;
    
    /**
     * 片段分类列表
     * @var array
     */
    private $categories = array();
    
    /**
     * 初始化管理器
     * @param Html|ParticleView $parent 父级对象
     */
    public function __construct( $parent ) {
        $this->parent = $parent;
    }
    
    /**
     * 获取当前片段管理器所属的视图
     * @return Html|ParticleView
     */
    public function getParent() {
        return $this->parent;
    }
    
    /**
     * 加载视图片段到当前管理器中, 如果名称已经存在， 
     * 则后添加的视图将会覆盖掉之前的片段。
     * @param string $name 片段名称
     * @param string|callable 片段处理器
     * @param string $category 分类名称
     * @return \X\Service\XAction\Component\WebView\ParticleView
     */
    public function load( $name, $handler, $category=null ) {
        $this->particles[$name] = new ParticleView($name, $handler, $this);
        if ( null !== $category ) {
            if ( !isset($this->categories[$category]) ) {
                $this->categories[$category] = array();
            }
            $this->categories[$category][] = $name;
        }
        return $this->particles[$name];
    }
    
    /**
     * 检查管理器中是否存在给定名称的视图片段。
     * @param string $name 片段名称
     * @return boolean
     */
    public function has( $name ) {
        return isset($this->particles[$name]);
    }
    
    /**
     * 根据名称获取视图片段
     * @param string $name 片段名称
     * @throws \Exception 视图不存在
     * @return \X\Service\XAction\Component\WebView\ParticleView
     */
    public function get( $name ) {
        if ( !$this->has($name) ) {
            throw new \Exception('Can not find particle view "'.$name.'"');
        }
        return $this->particles[$name];
    }
    
    /**
     * 获取所有片段名称
     * @return array
     */
    public function getList() {
        return array_keys($this->particles);
    }
    
    /**
     * 删除指定片段
     * @param string $name 片段名称
     */
    public function remove( $name ) {
        if ( isset($this->particles[$name]) ) {
            unset($this->particles[$name]);
        }
    }
    
    /**
     * 清空所有片段的渲染缓存
     * @return void
     */
    public function cleanUp() {
        foreach ( $this->particles as $particle ) {
            $particle->cleanUp();
        }
    }
    
    /**
     * 将所有片段渲染成字符串
     * @param string $category 分组名称
     * @return string
     */
    public function toString( $category=null ) {
        $content = array();
        
        $particles = array_keys($this->particles);
        if ( null !== $category ) {
            if ( !isset($this->categories[$category]) ) {
                return '';
            }
            $particles = $this->categories[$category];
        }
        
        foreach ( $particles as $particle ) {
            $content[] = $this->get($particle)->toString();
        }
        
        return implode("\n", $content);
    }
}