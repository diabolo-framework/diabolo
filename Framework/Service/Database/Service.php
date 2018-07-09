<?php
namespace X\Service\Database;
use X\Core\Service\XService;
class Service extends XService {
    /**
     * @param string|mixed $db
     * @return \X\Service\Database\Database
     */
    public function getDB( $db ) {
        if ( $db instanceof Database ) {
            return $db;
        }
        return new Database(null);
    }
}