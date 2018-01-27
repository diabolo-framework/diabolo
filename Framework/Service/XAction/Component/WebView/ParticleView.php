<?php
namespace X\Service\XAction\Component\WebView;
use X\Core\Component\ConfigurationArray;
/**
 * HTML 视图片段
 * @author Michael Luthor <michaelluthor@163.com>
 */
class ParticleView {
    /**
     * 视图名称
     * @var string
     */
    private $name = null;
    
    /**
     * 视图处理器
     * @var string|callable
     */
    private $handler = null;
    
    /**
     * 视图数据
     * @var ConfigurationArray
     */
    private $data = null;
    
    /**
     * 视图片段缓存内容
     * @var string
     */
    private $content = null;
    
    /**
     * 视图片段所属管理器
     * @var ParticleViewManager
     */
    private $manager = null;
    
    /**
     * 子片段管理器
     * @var ParticleViewManager
     */
    private $childParticles = null;
    
    /**
     * 初始化片段
     * @param string $name
     * @param string|callable $handler
     * @param ParticleViewManager $manager
     */
    public function __construct( $name, $handler, ParticleViewManager $manager=null ) {
        $this->name = $name;
        $this->handler = $handler;
        $this->data = new ConfigurationArray();
        $this->manager = $manager;
    }
    
    /**
     * 获取当前片段的数据管理器
     * @return \X\Core\Component\ConfigurationArray
     */
    public function getDataManager() {
        return $this->data;
    }
    
    /**
     * 获取当前片段所属的片段管理器
     * @return ParticleViewManager
     */
    public function getManager() {
        return $this->manager;
    }
    
    /**
     * 获取该片段的自片段管理器
     * @return \X\Service\XAction\Component\WebView\ParticleViewManager
     */
    public function getChildManager() {
        if ( null === $this->childParticles ) {
            $this->childParticles = new ParticleViewManager($this);
        }
        return $this->childParticles;
    }
    
    /**
     * 输出该视图片段
     * @return void
     */
    public function display() {
        echo $this->toString();
    }
    
    /**
     * 将该视图片段转换为字符串
     * @return string
     */
    public function toString() {
        if ( null === $this->content ) {
            $this->content = $this->doRender();
        }
        return $this->content;
    }
    
    /**
     * 执行渲染当前视图片段
     * @return string
     */
    private function doRender() {
        if ( is_string($this->handler) && is_file($this->handler) ) {
            extract($this->getViewRenderData());
            ob_start();
            ob_implicit_flush(false);
            require $this->handler;
            return ob_get_clean();
        } else if ( is_callable($this->handler) ) {
            return call_user_func_array($this->handler, array($this->getViewRenderData(), $this->getOptionManager()->toArray()));
        } else if ( is_string($this->handler) ) {
            return $this->handler;
        } else {
            return null;
        }
    }
    
    /**
     * 获取当前视图片段的数据， 如果当前片段属于某个视图， 
     * 则该视图的全局数据会被合并到该片段。
     * @return array
     */
    private function getViewRenderData(){
        $pageData = array();
        if ( null !== $this->manager ) {
            $pageData = $this->manager->getParent()->getDataManager()->toArray();
        }
        return array_merge($pageData,$this->getDataManager()->toArray());
    }
    
    /**
     * 清空当前片段的缓存渲染内容
     * @return void
     */
    public function cleanUp() {
        $this->content = null;
    }
}