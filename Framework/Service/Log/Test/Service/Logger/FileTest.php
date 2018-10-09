<?php
namespace X\Service\Log\Test\Service\Logger;
use PHPUnit\Framework\TestCase;
use X\Service\Log\Logger\ILogger;
use X\Service\Log\Logger\File;
class FileTest extends TestCase {
    public function test_database_logger() {
        $file = __DIR__.'/../../Resource/Data/test.log';
        $logger = new File(array(
            'path'    => $file,
            'maxSize' => 10,
            'enableDailyFile'   => true,
            'format' => '{prettyTime} [{levelName}] {content}',
            'logLeve' => ILogger::LV_DEBUG,
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
        
        @unlink($file.'-'.date('Ymd'));
        $logger->sync();
        $logContent = file_get_contents($file.'-'.date('Ymd'));
        $this->assertEquals(6, count(explode("\n", $logContent))-1);
        
        $logger->clean();
        $logs = $logger->getActiveLogs();
        $this->assertEquals(0, count($logs));
        
        $logger->trace('THIS IS A TRACE LOG');
        $logger->debug('THIS IS A DEBUG LOG');
        $logger->info('THIS IS AN INFO LOG');
        $logger->warn('THIS IS A WARN LOG');
        $logger->error('THIS IS AN ERROR LOG');
        $logger->fatal('THIS IS A FATAL LOG');
        $logger->sync();
        $this->assertTrue(file_exists($file.'-'.date('Ymd').'-1'));
    }
}