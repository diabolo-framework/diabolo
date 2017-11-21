<?php
namespace X\Core\Environment\Handler;
use X\Core\Environment\Util\Handler;
/**
 * Apache运行环境实例
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Apache extends Handler {
    /**
     * 获取当前运行环境名称
     * @see \X\Core\Environment\Util\Handler::getName()
     */
    public function getName() {
        return 'apache';
    }
    
    /**
     * 初始化运行参数
     * @see \X\Core\Environment\Util\Handler::initParameters()
     */
    protected function initParameters() {
        return array_merge($_GET, $_POST, $_REQUEST);
    }
}