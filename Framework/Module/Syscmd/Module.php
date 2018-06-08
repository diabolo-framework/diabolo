<?php
namespace X\Module\Syscmd;
use X\Core\Module\XModule;
use X\Service\XAction\Service as XActionService;
class Module extends XModule {
    /**
     * 使用 XAction 服务来处理请求。
     * @see \X\Core\Module\XModule::run()
     */
    public function run($parameters = array()) {
        $actionService = XActionService::getService();
        
        $namespace = get_class($this);
        $namespace = substr($namespace, 0, strrpos($namespace, '\\'));
        $group = $this->getName();
        $actionService->addGroup($group, $namespace.'\\Action');
        $actionService->setGroupOption($group, 'defaultAction', 'help');
        $actionService->setGroupOption($group, 'viewPath', $this->getPath('View/'));
        $actionService->getParameterManager()->merge($parameters);
        return $actionService->runGroup($group);
    }
}