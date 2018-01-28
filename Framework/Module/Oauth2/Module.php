<?php
namespace X\Module\Oauth2;
use X\Core\X;
use X\Core\Module\XModule;
use X\Module\Oauth2\Util\ActionBase;
class Module extends XModule {
    /**
     * {@inheritDoc}
     * @see \X\Core\Module\XModule::run()
     */
    public function run($parameters = array()) {
        if ( !isset($parameters['action']) ) {
            die('Parameter `action` is required.');
        }
        
        $action = $parameters['action'];
        $action = explode('/', $action);
        $action = array_map('ucfirst', $action);
        $action = implode('\\', $action);
        
        $actionClass = '\\X\\Module\\Oauth2\\Action\\'.$action;
        if ( !class_exists($actionClass) ) {
            die("Action {$parameters['action']} does not exists.");
        }
        
        /** @var $actionHandler ActionBase */
        $actionHandler = new $actionClass($parameters);
        $actionHandler->exec();
    }
}