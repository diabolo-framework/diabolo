<?php
namespace X\Service\Log\Test\Service;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Log\Service as LogService;
use X\Service\Log\Logger\ILogger;
class ServiceTest extends TestCase {
    /** */
    public function test_log_service( ) {
        $service = LogService::getService();
        $logger = $service->getLogger('another-logger');
        $logger->warn('WARNING, RUN !!!');
        $logger->error("THERE IS NO WAY OUT!!!");
        $logger->fatal("I AM HUNGERING~~~");
        $logger->info("I WANT FOOD");
        $logger->trace("I AM TAKING MY HAND");
        $logger->debug("TRY TO CATCH THAT SELLER");
        
        X::system()->log('THIS LOG COMES FROM MAGIC CALL');
        
        X::system()->log('THIS IS AN ERROR LOG', ILogger::LV_ERROR);
        
        
        
    }
}