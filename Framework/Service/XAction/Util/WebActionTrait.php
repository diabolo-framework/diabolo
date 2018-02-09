<?php
namespace X\Service\XAction\Util;
use X\Core\X;
trait WebActionTrait {
    /**
     * build up a url string.
     * @param string $path
     * @param array $params
     * @return string
     */
    public function createURL( $path, $params=null ) {
        $urlInfo = parse_url($path);
        if ( null !== $params ) {
            $parmConnector = (isset($urlInfo['query'])) ? '&' : '?';
            $path = $path.$parmConnector.http_build_query($params);
        }
        return $path;
    }
    
    /**
     * Jump to target url and exit the script.
     * @param string $url The target url to jump to.
     * @param array  $parms The parameters to that url
     */
    public function gotoURL( $url, $parms=null ) {
        $url = $this->createURL($url, $parms);
        header("Location: $url");
        X::system()->stop();
    }
    
    /**
     * Get referer url
     * @return string
     */
    public function getReferer( $default='/' ) {
        $url = isset($_SERVER['HTTP_REFERER']) ?  $_SERVER['HTTP_REFERER'] : $default;
        return $url;
    }
    
    /**
     * Go back to prev url by referer.
     * @return void
     */
    public function goBack() {
        $referer = $this->getReferer();
        $url = (null===$referer) ?   '/' : $referer;
        $this->gotoURL($url, null, false);
    }
    
    /**
     * @return boolean
     */
    public function isRequestedByPost() {
        return 'POST' === strtoupper($_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * @param unknown $name
     * @return boolean
     */
    public function hasUploadFile( $name ) {
        return isset($_FILES[$name]) && $_FILES[$name]['error'] != UPLOAD_ERR_NO_FILE;
    }
}