<?php
namespace X\Service\XAction\Handler;
use X\Core\X;
use X\Service\XAction\Service as XActionService;
use X\Service\XAction\Util\WebActionTrait;
use X\Service\XAction\Component\WebView\Html;
/**
 * 用于处理普通Web请求的动作并相应一个Web页面或者部分web页面。
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class WebPageAction extends \X\Service\XAction\Util\Action {
    /** Uses */
    use WebActionTrait;
    /**
     * 视图布局名称，需要在实现类中赋值，否则无法输出视图。
     * 当名称以"/"开头时，取全局布局文件， 否则取该动作所在
     * 分组的视图路径中取布局文件。
     * @var string
     */
    protected $layout = null;
    /**
     * 当前页面标题
     * @var string
     */
    protected $title = "";
    /**
     * 视图实例, 在动作初始化时实例化该视图
     * @var Html
     */
    private $view = null;
    
    /**
     * 初始化Web动作
     * @see \X\Service\XAction\Util\Action::init()
     * @return void
     */
    protected function init() {
        parent::init();
        $this->view = new Html();
    }
    
    /**
     * 如果只为真则跳转到404错误页面。
     * @param boolean $value
     * @return void
     */
    public function throw404IfTrue( $value ) {
        if ( true === $value ) {
            XActionService::getService()->triggerErrorHandler('404');
            X::system()->stop();
        }
    }
    
    /**
     * 获取当前动作的视图
     * @return \X\Service\XAction\Component\WebView\Html
     */
    public function getView() {
        return $this->view;
    }
    
    /**
     * 增加数据到视图
     * @param string $name 数据名
     * @param mixed $value 数据值
     * @param string $view 视图名称，默认为视图全局数据
     */
    public function addDataToView( $name, $value, $view=null ) {
        if ( null === $view ) {
            $this->view->getDataManager()->set($name, $value);
        } else {
            $this->view->getParticleViewManager()->get($view)->getDataManager()->set($name, $value);
        }
    }
    
    /**
     * 增加视图片段
     * @param string $view 视图名称，
     *      例如
     *      "demo/view001" 将取当前组的视图文件夹下面的demo/view001.php文件。<br>
     *      "/view001" 将取公共片段 view001.php文件
     * @param array $data 传递给视图的数据
     * @param string $category 视图分类
     * @return \X\Service\XAction\Component\WebView\ParticleView
     */
    public function addParticle($view, $data=array(), $category=null) {
        $viewPath = $this->getViewPathByName($view, 'Particle');
        $particle = $this->view->getParticleViewManager()->load($view, $viewPath, $category);
        $particle->getDataManager()->merge($data);
        return $particle;
    }
    
    /**
     * 通过视图名称获取视图真是路径.
     * @param string $name 视图名称
     * @param string $type 视图类型， 例如"Particle", "Layout"
     * @return string
     */
    protected function getViewPathByName($name, $type) {
        $service = XActionService::getService();
        
        $viewPath = null;
        $fileName = $name;
        if ( '/' === $name[0] ) {
            $viewPath = X::system()->getPath($service->getConfiguration()->get('CommonViewPath', 'View/'));
            $fileName = substr($name, 1);
        } else {
            $viewPath = $service->getGroupOption($this->getGroupName(), 'viewPath');
        }
        
        $viewPath = rtrim($viewPath, DIRECTORY_SEPARATOR);
        $viewPath .= DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$fileName.'.php';
        return $viewPath;
    }
    
    /**
     * 渲染一个视图片段
     * @param string $view 视图名称， <br>
     * 例如<br>
     * "demo/view001" 将取当前组的视图文件夹下面的demo/view001.php文件。<br>
     * "/view001" 将取公共片段 view001.php文件
     * @param array $data 传递给视图的数据
     * @return void
     */
    public function displayParticle($view, $data=array()) {
        $particle = $this->addParticle($view, $data);
        $particle->display();
    }
    
    /**
     * 渲染当前动作的视图
     * @return void
     */
    public function display() {
        if ( null !== $this->layout ) {
            $this->view->setLayout($this->getViewPathByName($this->layout, 'Layout'));
        }
        $this->view->title = $this->title;
        $this->view->display();
    }
}