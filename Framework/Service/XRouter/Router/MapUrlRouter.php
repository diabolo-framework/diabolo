<?php
namespace X\Service\XRouter\Router;
use X\Service\XRouter\Service;
class MapUrlRouter implements RouterInterface {
    /** @var string */
    private $fakeExt = null;
    /** @var array */
    private $regexAlias = array();
    /** @var array */
    private $rules = array(
        #array('source' => 'url','target' => 'url','sourceRegex' => '');
    );
    
    /**
     * @param Service $service
     */
    public function __construct( Service $service) {
        $config = $service->getConfiguration();
        $this->fakeExt = $config->get('fakeExt', null);
        $this->regexAlias = $config->get('regexAlias', array());
        
        $this->initRules($config->get('rules', array()));
    }
    
    /** @return void */
    private function initRules( $rules ) {
        foreach ( $rules as $source => $target ) {
            $regex = $source;
            $rule = array('srouce'=>$source, 'target'=>$target);
            preg_match_all('#\{(?P<var>.*?)\}#', $source, $matchedVars);
            foreach ( $matchedVars['var'] as $index => $varDef ) {
                $varName = $varDef;
                $varReg = '.*?';
                
                if ( false !== strpos($varDef, ':') ) {
                    $varDef = explode(':', $varDef);
                    $varName = $varDef[0];
                    
                    $varDefMark = $varDef[1][0];
                    $varDefCon = substr($varDef[1], 1);
                    switch ( $varDefMark ) {
                    case '@' : 
                        $varReg = $varDefCon; 
                        break;
                    case '$' : 
                        if ( !isset($this->regexAlias[$varDefCon]) ) {
                            throw new \Exception("XRequest router : no alias for `{$varDefCon}`");
                        }
                        $varReg = $this->regexAlias[$varDefCon]; 
                        break;
                    default : 
                        throw new \Exception("XRequest router : unsported var defination for `{$varName}`"); 
                        break;
                  }
                } else if ( isset($this->regexAlias[$varName]) ) {
                    $varReg = $this->regexAlias[$varName];
                }
                
                $varReg = "(?P<{$varName}>{$varReg})";
                $regex = str_replace($matchedVars[0][$index], $varReg, $regex);
            }
            $rule['sourceRegex'] = "#{$regex}#is";
            
            $this->rules[] = $rule;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\XRequest\RouterInterface::route()
     * @example /{module:$module} => module={module}&action=index
     * @example /{module:$module}/{target:$id} => module={mdoule}&action=detail&{module}={id}
     */
    public function route($url) {
        $oriQuery = null;
        if ( null !== $this->fakeExt ) {
            $urlInfo = parse_url($url);
            $oriQuery = isset($urlInfo['query']) ? $urlInfo['query'] : null;
            $sourceUrl = substr($urlInfo['path'], 0, (strlen($this->fakeExt)+1)*-1);
        }
        
        $result = null;
        foreach ( $this->rules as $rule ) {
            if ( !preg_match($rule['sourceRegex'], $sourceUrl, $vars) ) {
                continue;
            }
            
            $result = $rule['target'];
            preg_match_all('#\{(?P<var>.*?)\}#', $rule['target'], $matchedVars);
            $matchedVars['var'] = array_unique($matchedVars['var']);
            foreach ( $matchedVars['var'] as $varName ) {
                $result = str_replace("{{$varName}}", $vars[$varName], $result);
            }
            break;
        }
        
        if ( null !== $oriQuery ) {
            $result = $result.'&'.$oriQuery;
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\XRequest\RouterInterface::format()
     */
    public function format($url) {
        throw new \Exception("XRouter handler MapUrl does not support format url.");
    }
}