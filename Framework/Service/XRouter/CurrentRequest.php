<?php
namespace X\Service\XRouter;
class CurrentRequest {
    public static function buildFromCurrentRequest() {
        return new self();
    }
    
    /** @return string */
    public function getUrl() {
        return $_SERVER['REQUEST_URI'];
    }
}