<?php
namespace X\Service\Router\Router;
use X\Core\X;
use X\Service\Router\RouterException;
class ActionPath extends RouterBase {
    /** @var string */
    protected $defaultModuleName = null;
    /** @var boolean */
    protected $mergeParamIntoPath = false;
    /** @var boolean */
    protected $pathActionParamSeparator = '_';
    /** @var string */
    protected $defaultActionName = 'detail';
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Router\Router\RouterBase::router()
     */
    public function router($url) {
        $pathPart = $this->parsePathParts($this->getUrlPath($url));
        $this->setupModuleForPath($pathPart);
        $this->setupDefaultActionName($pathPart);
        
        $baseParams = $this->groupPathPartsToParams($pathPart);
        $baseParams = array_merge($baseParams,$this->getUrlQueryParams($url));
        return $baseParams;
    }
    
    /**
     * @return array
     */
    private function groupPathPartsToParams( $urlPath ) {
        $module = $urlPath[0]['name'];
        $action = array();
        $params = array();
        
        foreach ( $urlPath as $item ) {
            if ( !$item['isModule'] ) {
                $action[] = $item['name'];
            }
            if ( null !== $item['paramValue'] ) {
                $params[$item['name']] = $item['paramValue'];
            }
        }
        $params['module'] = $module;
        $params['action'] = implode('/', $action);
        return $params;
    }
    
    /**
     * @param unknown $urlPath
     * @return array
     */
    private function parsePathParts( $urlPath ) {
        $urlPath = ltrim($urlPath, '/');
        $urlPath = explode('/', $urlPath);
        foreach ( $urlPath as $index => $urlPart ) {
            $action = $urlPart;
            $paramValue = null;
            if ( $this->mergeParamIntoPath && false!==strpos($urlPart, $this->pathActionParamSeparator) ) {
                list($action, $paramValue) = explode($this->pathActionParamSeparator,$urlPart);
            }
            
            $urlPath[$index] = array(
                'isModule' => (0===$index),
                'name' => $action,
                'paramValue' => $paramValue,
            );
        }
        return $urlPath;
    }
    
    /** */
    private function setupDefaultActionName( array &$urlPath ) {
        if ( null === $this->defaultActionName ) {
            return;
        }
        
        $last = count($urlPath) - 1;
        if ( null !== $urlPath[$last]['paramValue'] ) {
            $urlPath[] = array(
                'isModule' => false,
                'name' => $this->defaultActionName,
                'paramValue' => null,
            );
        }
    }
    
    /**
     * @param array $path
     */
    private function setupModuleForPath( array &$urlPath ) {
        $moduleName = $urlPath[0]['name'];
        if ( X::system()->getModuleManager()->has(ucfirst($moduleName)) ) {
            return;
        }
        if ( null === $this->defaultModuleName ) {
            throw new RouterException('can not find module name in url');
        }
        
        $moduleName = ucfirst($this->defaultModuleName);
        if ( !X::system()->getModuleManager()->has($moduleName) ) {
            throw new RouterException('default module name is not available');
        }
        
        $urlPath[0]['isModule'] = false;
        array_unshift($urlPath, array(
            'isModule' => true,
            'name' => lcfirst($moduleName),
            'paramValue' => null,
        ));
    }
}