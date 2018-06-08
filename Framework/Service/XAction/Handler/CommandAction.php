<?php
namespace X\Service\XAction\Handler;
use X\Core\X;
/**
 * 命令行动作基类
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class CommandAction extends \X\Service\XAction\Util\Action {
    /**
     * {@inheritDoc}
     * @see \X\Service\XAction\Util\Action::doRunAction()
     */
    protected function doRunAction($parameters) {
        $handlerName = 'runAction';
        if ( !method_exists($this, $handlerName) || !is_callable(array($this, $handlerName)) ) {
            throw new \Exception("Can not find action handler \"runAction()\".");
        }
        
        $paramsToMethod = array();
        $class = new \ReflectionClass($this);
        $method = $class->getMethod($handlerName);
        
        $parameterInfos = $method->getParameters();
        foreach ( $parameterInfos as $index => $parmInfo ) {
            /* @var $parmInfo \ReflectionParameter */
            $name = $parmInfo->getName();
            if ( isset($parameters['params'][$index]) ) {
                $paramsToMethod[$name] = $parameters['params'][$index];
            } else if ( $parmInfo->isOptional() && $parmInfo->isDefaultValueAvailable() ) {
                $paramsToMethod[$name] = $parmInfo->getDefaultValue();
            } else {
                throw new \Exception('Parameters to action handler is not available.');
            }
        }
        
        # setup options
        foreach ( $parameters['options'] as $key => $value ) {
            $key = lcfirst($this->convertToUpperCamelBySeparator($key, '-'));
            if ( property_exists($this, $key) ) {
                $this->$key = $value;
            }
        }
        
        $handler = array($this, $handlerName);
        return \call_user_func_array($handler, $paramsToMethod);
    }
    
    /**
     * Convert to Upper Camel (LikeThis) by separator.
     * @param string $string
     * @return string
     */
    protected function convertToUpperCamelBySeparator( $string, $separator, $glue='' ) {
        $string = explode($separator, $string);
        return implode($glue, array_map('ucfirst', $string));
    }
    
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