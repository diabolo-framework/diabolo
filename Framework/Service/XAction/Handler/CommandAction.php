<?php
namespace X\Service\XAction\Handler;
use X\Core\X;
/**
 * 命令行动作基类
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class CommandAction extends \X\Service\XAction\Util\Action {
    /**
     * 退出运行
     * @return void
     */
    public function quit() {
        X::system()->stop();
    }
    
    /**
     * 输出一行内容到控制台，并且自动追加换行.
     * @param string $string 内容
     * @return mixed
     */
    public function writeLine( $string ) {
        $args = func_get_args();
        $args[0] .= "\n";
        return call_user_func_array(array($this, 'write'), $args);
    }
    
    /**
     * 输出内容到控制台
     * @param string $string
     * @return void
     */
    public function write( $string ) {
        if ( 1 == func_num_args() ) {
            echo $string;
        } else {
            call_user_func_array('printf', func_get_args());
        }
    }
}