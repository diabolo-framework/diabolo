<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Util;
/**
 * 
 */
use X\Core\X;
use X\Service\XDatabase\Service as XDatabaseService;
/**
 * Func
 * Abstract class for sql functions.
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
abstract class Func {
    /**
     * Convert this function to string for safty use in query.
     * @return string
     */
    abstract public function toString();
    
    /**
     * quote the name of column
     * @param string $name
     * @return string
     */
    protected function quoteColumnName( $name ) {
        /* @var $service XDatabaseService */
        $service = X::system()->getServiceManager()->get(XDatabaseService::getServiceName());
        $database = $service->get();
        
        $column = explode('.', $name);
        $column = array_map(array($database, 'quoteColumnName'), $column);
        $column = implode('.', $column);
        return $column;
    }
}