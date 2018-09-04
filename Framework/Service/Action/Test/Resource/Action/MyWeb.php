<?php
namespace X\Service\Action\Test\Resource\Action;
use X\Service\Action\Handler\WebAction;
class MyWeb extends WebAction {
    /** @var string */
    protected $layout = 'Test';
    /** @var string */
    protected $title = 'TESTING';
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Action\Handler\ApiAction::run()
     */
    protected function run( ) {
        $webView = $this->getView();
        $style = $webView->getStyleManager();
        $style->add('body', array(
            'background-color' => 'red',
            'height' => '100%',
        ));
        $style->add('#main', array(
            'background-color' => 'white',
            'height' => '1000%',
        ));
        
        $link = $webView->getLinkManager();
        $link->addCSS('bootstrap', '//www.google.com/cdn/bootstrap/3.14.1/boostrap.min.css');
        
        $meta = $webView->getMetaManager();
        $meta->setCopyright('Diabolo');
        $meta->setCharset('UTF-8');
        
        $script = $webView->getScriptManager();
        $script->add('jquery', '//www.google.com/cdn/jquery/12.0.0/jquery.min.js');
        $script->setValue('version', '0.0.0');
        $script->add('cus')->setContent('alert("OK")');
        $script->add('cus2')->setContent('alert("2");');
        
        $this->addDataToView('globalKey001', 'globalKey001');
        $this->addParticle('Particle001', array('key001'=>'value001'));
        
        ob_start();
        ob_implicit_flush(false);
        $this->display();
        $content = ob_get_clean();
        return $content;
    }
}