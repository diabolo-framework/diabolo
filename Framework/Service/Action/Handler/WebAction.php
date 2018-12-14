<?php
namespace X\Service\Action\Handler;
use X\Core\X;
use X\Service\Action\Component\WebView\Html;
use X\Service\Action\Service as ActionService;
/**
 * 用于处理普通Web请求的动作并相应一个Web页面或者部分web页面。
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class WebAction extends ActionBase {
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
     * 获取当前动作的视图
     * @return \X\Service\XAction\Component\WebView\Html
     */
    protected function getView() {
        if ( null === $this->view ) {
            $this->view = new Html();
        }
        return $this->view;
    }
    
    /**
     * 增加数据到视图
     * @param string $name 数据名
     * @param mixed $value 数据值
     * @param string $view 视图名称，默认为视图全局数据
     */
    protected function addDataToView( $name, $value, $view=null ) {
        $webView = $this->getView();
        if ( null === $view ) {
            $webView->getDataManager()->set($name, $value);
        } else {
            $webView->getParticle($view)->set($name, $value);
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
    protected function addParticle($view, $data=array(), $category=null) {
        $webView = $this->getView();
        
        $viewPath = $this->getViewPathByName($view, 'Particle');
        $particle = $webView->getParticleViewManager()->load($view, $viewPath, $category);
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
        $viewPath = null;
        $fileName = $name;
        
        if ( is_file($name) ) {
            return $name;
        } else if ( '/' === $name[0] ) {
            $viewPath = ActionService::getService()->getGlobalViewPathByName(substr($name, 1), $type);
        } else {
            $viewPath = $this->getGroup()->getViewPath();
            $viewPath = rtrim($viewPath, DIRECTORY_SEPARATOR);
            $viewPath .= DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$fileName.'.php';
        }
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
    protected function displayParticle($view, $data=array()) {
        $particle = $this->addParticle($view, $data);
        $particle->display();
    }
    
    /**
     * 渲染当前动作的视图
     * @return void
     */
    protected function display() {
        $webView = $this->getView();
        
        if ( null !== $this->layout ) {
            $webView->setLayout($this->getViewPathByName($this->layout, 'Layout'));
        }
        $webView->title = $this->title;
        $webView->display();
    }
    
    /**
     * build up a url string.
     * @param string $path
     * @param array $params
     * @return string
     */
    protected function createURL( $path, $params=null ) {
        $urlInfo = parse_url($path);
        if ( null !== $params ) {
            $parmConnector = (isset($urlInfo['query'])) ? '&' : '?';
            $path = $path.$parmConnector.http_build_query($params);
        }
        return $path;
    }
    
    /**
     * Jump to target url and exit the script.
     * @param string $url The target url to jump to.
     * @param array  $parms The parameters to that url
     */
    protected function gotoURL( $url, $parms=null ) {
        $url = $this->createURL($url, $parms);
        header("Location: $url");
        X::system()->stop();
    }
    
    /**
     * Get referer url
     * @return string
     */
    protected function getReferer( $default='/' ) {
        $url = isset($_SERVER['HTTP_REFERER']) ?  $_SERVER['HTTP_REFERER'] : $default;
        return $url;
    }
    
    /**
     * Go back to prev url by referer.
     * @return void
     */
    protected function goBack() {
        $referer = $this->getReferer();
        $url = (null===$referer) ?   '/' : $referer;
        $this->gotoURL($url, null, false);
    }
}