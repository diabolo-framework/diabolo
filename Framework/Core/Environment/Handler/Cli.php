<?php
namespace X\Core\Environment\Handler;
use X\Core\Environment\Util\Handler;
/**
 * command line interface handler
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Cli extends Handler {
    /**
     * get handler name
     * @see \X\Core\Environment\Util\Handler::getName()
     */
    public function getName() {
        return 'cli';
    }
    
    /**
     * init the parameters
     * @see \X\Core\Environment\Util\Handler::initParameters()
     */
    protected function initParameters() {
        global $argv;
        $parameters = $this->parseParameters($argv);
        return $parameters;
    }
    
    /**
     * parse parameters from command line interface args.
     * @param array $path
     * @return array
     */
    private function parseParameters( $argv ) {
        $argv = $this->cleanUpAndSetupArgs($argv);
        
        $path = array_shift($argv);
        array_shift($path);
        $module = array_shift($path);
        $action = implode('/', $path);
        
        $actionParams = array();
        $actionOptions = array();
        foreach ( $argv as $value ) {
            if ( '-'===$value[0] && '-'===$value[1] ) {
                $sepPos = strpos($value, '=');
                $actionOptions[substr($value, 2, $sepPos-2)] = substr($value, $sepPos+1);
            } else {
                $actionParams[] = $value;
            }
        }
        return array(
            'module' => $module,
            'action' => $action,
            'params' => $actionParams,
            'options' => $actionOptions,
        );
    }
    
    /**
     * clean and setup args
     * @param array $argv
     * @return array
     */
    private function cleanUpAndSetupArgs( $argv ) {
        array_shift($argv); # remove script name
        if ( 0 === count($argv) ) {
            $argv[0] = 'help'; # add default action
        }
        if ( 'service' === substr($argv[0], 0, 7) ) {
            # redirect service action to executor in syscmd module.
            array_unshift($argv, 'module/syscmd/service/exec'); 
        }
        
        $path = explode('/',$argv[0]);
        if ( 1 === count($path) ) {
            array_unshift($path, 'syscmd');
        }
        if ( 2 === count($path) || 'syscmd'===$path[0]) {
            array_unshift($path, 'module');
        }
        $argv[0] = $path;
        return $argv;
    }
}