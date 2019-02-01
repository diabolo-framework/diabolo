<?php
namespace X\Service\Action\Component\WebView;
use X\Service\Action\ActionException;

/**
 * Base class for widget
 * @author michael
 */
abstract class WidgetBase {
    /** @var Html */
    private $hostView = null;
    
    /** @var string Name of the view to render */
    protected $widgetViewName = null;
    /** @var array Data to widget view. */
    protected $widgetViewData = array();
    
    /**
     * @param array $options
     * @return self
     */
    public static function setup( $options=array(), Html $hostView=null ) {
        $widget = new static();
        $widget->init();
        $widget->hostView = $hostView;
        foreach ( $options as $attr => $value ) {
            $widget->$attr = $value;
        }
        return $widget;
    }
    
    /**
     * @return null
     */
    protected function init() { 
        return null;
    }
    
    /**
     * Set host view
     * @param Html $htmlView
     */
    public function setHostView( Html $hostView ) {
        $this->hostView = $hostView;
        return $this;
    }
    
    /**
     * @return \X\Service\XAction\Component\WebView\Html
     */
    protected function getHostView() {
        return $this->hostView;
    }
    
    /**
     * @return void
     */
    public function display() {
        $viewPath = $this->getViewPath();
        if ( !file_exists($viewPath) ) {
            throw new ActionException("widget view `{$viewPath}` does not exists");
        }
        echo $this->renderView($viewPath, array_merge(
            $this->widgetViewData, 
            array('hostView'=>$this->hostView, 'widget'=>$this)
        ));
    }
    
    /**
     * 渲染视图文件
     * @param string $view
     * @param array $data
     * @return string|unknown
     */
    protected function renderView($view, $data) {
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
     * Get the path of current widget view
     * @return string
     */
    private function getViewPath() {
        if ( null === $this->widgetViewName ) {
            throw new ActionException('widget view name can not be null');
        }
        $widgetClassInfo = new \ReflectionClass($this);
        $basepath = dirname($widgetClassInfo->getFileName());
        return implode(DIRECTORY_SEPARATOR, array($basepath,'View',$this->widgetViewName.'.php'));
    }
}