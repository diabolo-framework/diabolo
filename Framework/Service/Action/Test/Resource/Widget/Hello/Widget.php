<?php
namespace X\Service\Action\Test\Resource\Widget\Hello;
use X\Service\Action\Component\WebView\WidgetBase;
class Widget extends WidgetBase {
    /** @var string */
    protected $user = null;
    /** @var string */
    protected $widgetViewName = 'Hello';
    
    /** @return string */
    public function getUser() {
        return $this->user;
    }
}