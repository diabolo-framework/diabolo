<?php
namespace X\Service\Log\Logger;
use X\Service\Log\LogContent;

interface ILogger {
    const LV_FATAL  = 6;
    const LV_ERROR  = 5;
    const LV_WARN   = 4;
    const LV_INFO   = 3;
    const LV_DEBUG  = 2;
    const LV_TRACE  = 1;
    
    function log( $content, $level=self::LV_INFO );
    function fatal( $content );
    function error( $content );
    function warn( $content );
    function info( $content );
    function debug( $content );
    function trace( $content );
    
    /**
     * get current logs which not been cleaned.
     * @return LogContent[]
     */
    function getActiveLogs();
    function sync();
    function clean();
}