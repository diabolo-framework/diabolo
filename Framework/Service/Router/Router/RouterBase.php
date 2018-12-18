<?php
namespace X\Service\Router\Router;
abstract class RouterBase {
    /** @var string */
    protected $fakeExtension = null;
    
    /**
     * @param unknown $options
     */
    public function __construct( $options ) {
        foreach ( $options as $key => $value ) {
            if ( property_exists($this, $key) ) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * @param unknown $url
     * @param string $withoutFaceExt
     * @return string|mixed
     */
    protected function getUrlPath( $url, $withoutFaceExt=true ) {
        $path = parse_url($url, PHP_URL_PATH);
        if ( null === $path ) {
            return '/';
        }
        if ( $withoutFaceExt && null !== $this->fakeExtension ) {
            $fakeExt = '.'.$this->fakeExtension;
            $fakeExtLength = strlen($fakeExt);
            if ( $fakeExt === substr($path, -1*$fakeExtLength ) ) {
                $path = substr($path, 0, $path-$fakeExtLength);
            }
        }
        return $path;
    }
    
    /**
     * @param unknown $url
     * @return array
     */
    protected function getUrlQueryParams( $url ) {
        $query = parse_url($url, PHP_URL_QUERY);
        if ( null === $query ) {
            return array();
        }
        
        $params = array();
        parse_str($query, $params);
        return $params;
    }
    
    /**
     * @param string $url
     * @return array
     */
    abstract function router ($url);
}