<?php
namespace X\Core\Environment\Handler;
use X\Core\Environment\Util\Handler;
/**
 * 命令行运行环境实例
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Cli extends Handler {
    /**
     * 获取当前运行环境名称
     * @see \X\Core\Environment\Util\Handler::getName()
     */
    public function getName() {
        return 'cli';
    }
    
    /**
     * 初始化运行参数
     * @see \X\Core\Environment\Util\Handler::initParameters()
     */
    protected function initParameters() {
        global $argv;
        $parameters = array();
        foreach ( $argv as $index => $parm ) {
            if ( '--' !== substr($parm, 0, 2) ) {
                continue;
            }
            $parm = explode('=', $parm);
            $name = $parm[0];
            $value = isset($parm[1]) ? trim($parm[1]) : true;
            $name = substr($name, 2);
            $parameters[trim($name)] = $value;
        }
        return $parameters;
    }
}