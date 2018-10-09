<?php
namespace X\Service\Log\Test\Service\Logger;
use PHPUnit\Framework\TestCase;
use X\Service\Log\Logger\Database;
use X\Service\Log\Logger\ILogger;
use X\Service\Database\Query;
class DatabaseTest extends TestCase {
    public function test_database_logger() {
        $dbname = 'logdb';
        $tableName = 'log';
        
        $logger = new Database(array(
            'dbname'    => $dbname,
            'tableName' => $tableName,
            'columns'   => array(
                'logged_at' => 'loggedAt',
                'content' => 'content',
                'level' => 'level',
                'level_name' => 'levelName',
                'user_name' => function( $log ) {
                    return 'demo-user';
                },
                'synced_at' => 'time',
                'sapi_name' => 'sapiName',
            ),
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
        
        Query::truncateTable($dbname)->table($tableName)->exec();
        $logger->sync();
        $logCount = Query::select($dbname)->from($tableName)->count();
        $this->assertEquals(6, $logCount);
        
        $logger->clean();
        $logs = $logger->getActiveLogs();
        $this->assertEquals(0, count($logs));
    }
}