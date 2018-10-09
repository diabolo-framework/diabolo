<?php
namespace X\Service\Log\Test\Service\Logger;
use PHPUnit\Framework\TestCase;
use X\Service\Log\Logger\ILogger;
use X\Service\Log\Logger\Socket;
class SocketTest extends TestCase {
    public function test_database_logger() {
        $logger = new Socket(array(
            'logLeve' => ILogger::LV_DEBUG,
            'protocol' => SOL_TCP,
            'address' => '127.0.0.1',
            'port' => 55555,
            'format' => '{prettyTime} [{levelName}] {content}',
        ));
        
        $logger->trace('THIS IS A TRACE LOG');
        $logger->debug('THIS IS A DEBUG LOG');
        $logger->info('THIS IS AN INFO LOG');
        $logger->warn('THIS IS A WARN LOG');
        $logger->error('THIS IS AN ERROR LOG');
        $logger->fatal('THIS IS A FATAL LOG');
        
        $logger->log('THIS IS A CUSTOM LEVEL LOG', ILogger::LV_FATAL);
        
        $logs = $logger->getActiveLogs();
        $this->assertEquals(6, count($logs));
        
        $logger->sync();
        
        $logger->clean();
        $logs = $logger->getActiveLogs();
        $this->assertEquals(0, count($logs));
    }
}