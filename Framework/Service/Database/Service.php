<?php
namespace X\Service\Database;
use X\Core\Service\XService;
class Service extends XService {
    /**
     * @param unknown $name
     * @return \X\Service\Database\Database
     */
    public function getDB( $name ) {
        return new Database(null);
    }
}