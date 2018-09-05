<?php 
namespace X\Service\Error\Test\Service;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Error\Service;
class ServiceTest extends TestCase {
   /**
    */
    protected function setup() {
        $service = Service::getService();
        if ( Service::STATUS_RUNNING !== $service->getStatus() ) {
            $service->start();
            X::system()->registerMagicHandler('mailRuntimeError', function( $mail ) {
                echo "EMAIL ERROR HANDLER\n";
            });
        }
    }
    
    /***/
    public function test_exception() {
        Service::getService()->start();
        
        ob_start();
        ob_implicit_flush(false);
        Service::getService()->handleException(new \Exception("TEST_EXCEPTION"));
        $content = ob_get_clean();
        
        $this->assertEquals('EMAIL ERROR HANDLER
FUNCTION CALL HANDLER
VIEW HANDLER', $content);
    }
    
    /***/
    public function test_error() {
        Service::getService()->start();
        
        ob_start();
        ob_implicit_flush(false);
        trigger_error('TEST_ERROR');
        $content = ob_get_clean();
        
        $this->assertEquals('EMAIL ERROR HANDLER
FUNCTION CALL HANDLER
VIEW HANDLER', $content);
    }
}