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
        
        public function assertEquals( $expected, $actual ) {
            if ( $expected != $actual ) {
                throw new \Exception("assertEquals failed : 期望={$expected}; 实际={$actual}");
            }
        }
        
        public function assertArrayHasKey($key, $array ) {
            if ( !array_key_exists($key, $array) ) {
                throw new \Exception("assertArrayHasKey failed : 键名={$key}");
            }
        }
        
    }
}