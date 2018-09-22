<?php
namespace X\Core\Component;
class Stringx {
    /**
     * generate random string
     * @param integer $length
     * @return string
     */
    public static function random( $length ) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_pad($chars, $length, $chars)), 0, $length);
    }
    
    /**
     * generate uuid string
     * @return string
     */
    public static function uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
    
    /**
     * convert snake case to camel case
     * @param unknown $string
     * @return string
     */
    public static function snakeToCamel( $string ) {
        return implode('', ucwords(str_replace('_', ' ', $string)));
    }
    
    /**
     * convert snake case to camel case
     * @param unknown $string
     * @return string
     */
    public static function middleSnakeToCamel( $string ) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    
    /**
     * convert camel case to snake case
     * @param unknown $string
     * @return string
     */
    public static function camelToSnake( $string ) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}