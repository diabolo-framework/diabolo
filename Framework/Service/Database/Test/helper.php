<?php
namespace PHPUnit\Framework {
    class TestCase {
        public function __construct() {
           $this->setUp();
        }
        
        public function clean() {
            $this->tearDown();
        }
        
        public function __call( $name, $params ) {
            
        }
    }
}