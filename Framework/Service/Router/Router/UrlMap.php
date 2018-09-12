<?php
namespace X\Service\Router\Router;
class UrlMap extends RouterBase {
    /** @var array */
    protected $map = array(
        # $source => $target
    );
    
    /** @var array */
    private $mapCached = array(
        # source => array(
        #     regex => '',
        #     params => array('module','action')
        #),
    );
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Router\RouterInterface::router()
     */
    public function router($url) {
        foreach ( $this->map as $source => $target ) {
            $params = $this->processUrl($source, $this->getUrlPath($url));
            if ( false === $params ) {
                continue;
            }
            $params = $this->getUrlQueryParams($url) + $params;
            return $params;
        }
        return false;
    }
    
    /**
     * @param unknown $source
     * @param unknown $url
     */
    private function processUrl( $source, $url ) {
        if ( !isset($this->mapCached[$source]) ) {
            $this->cacheUrlMap($source);
        }
        
        if ( !preg_match($this->mapCached[$source]['regex'], $url, $matchedParams) ) {
            return false;
        }
        
        $targetUrl = $this->map[$source];
        foreach ( $this->mapCached[$source]['params'] as $pname ) {
            $targetUrl = str_replace('{'.$pname.'}', $matchedParams[$pname], $targetUrl);
        }
        
        $queryString = parse_url($targetUrl, PHP_URL_QUERY);
        if ( null === $queryString ) {
            return array();
        }
        
        parse_str($queryString, $queryParams);
        return $queryParams;
    }
    
    /**
     * <li>/ => /</li>
     * <li>/{module}/{action} => 'index.php?module={module}&action={action}&test=1'</li>
     * @param unknown $source
     */
    private function cacheUrlMap( $source ) {
        preg_match_all('#\\{(?P<name>.*?)\\}#is',$source, $paramsMatched);
        
        $regex = '#^'.$source.'$#is';
        $params = array();
        foreach ( $paramsMatched['name'] as $pname ) {
            $params[] = $pname;
            $regex = str_replace('{'.$pname.'}', '(?P<'.$pname.'>.*?)', $regex);
        }
        
        $this->mapCached[$source] = array(
            'regex' => $regex,
            'params' => $params,
        );
    }
}