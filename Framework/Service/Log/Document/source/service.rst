Service
=======
a service to handle runtime logs, 

suppported log levels are :

- trace
- debug
- info
- warn
- error
- fatal

Configuration
-------------
example : ::

   # config this to support database storage
   'Database' => array(
        'class' => \X\Service\Database\Service::class,
        'enable' => true,
        'delay' => true,
        'params' => array(
            'databases' => array(
                'logdb' => array(
                    'driver' => \X\Service\Database\Driver\Sqlite::class,
                    'path' => __DIR__.'/Resource/Data/log.db',
                ),
            ),
        ),
    ),
    
    'Log' => array(
        'class' => \X\Service\Log\Service::class,
        'enable' => true,
        'delay' => true,
        'params' => array(
            'defaultLogger' => 'syslogLogger',
            'loggers' => array(
                'databaseLogger' => array(
                    'logger'    => \X\Service\Log\Logger\Database::class,
                    'dbname'    => 'logdb',
                    'tableName' => 'log',
                    'logLeve'   => 'debug',
                    'columns'   => array(
                        'logged_at'  => 'loggedAt',
                        'content'    => 'content',
                        'level'      => 'level',
                        'level_name' => 'levelName',
                        'user_name'  => function( $log ) {
                            return 'demo-user';
                        },
                        'synced_at'  => 'time',
                        'sapi_name'  => 'sapiName',
                    ),
                ),
                'fileLogger' => array(
                    'logger'          => \X\Service\Log\Logger\File::class,
                    'path'            => '/home/demo/log/log',
                    'maxSize'         => 10,
                    'enableDailyFile' => true,
                    'format'          => '{prettyTime} [{levelName}] {content}',
                    'logLeve'         => 'debug',
                ),
                'socketLogger' => array(
                    'logger'    => \X\Service\Log\Logger\Socket::class,
                    'logLeve'   => 'debug',
                    'protocol'  => SOL_TCP,
                    'address'   => '127.0.0.1',
                    'port'      => 55555,
                    'format'    => '{prettyTime} [{levelName}] {content}',
                ),
                'syslogLogger' => array(
                    'logger'    => \X\Service\Log\Logger\Syslog::class,
                    'logLeve'   => 'debug',
                    'ident'     => 'diabolo-log-test',
                    'facility'  => LOG_USER,
                ),
            ),
        ),
    ),

Basic Usage
-----------
quick log : ::

    use X\Core\X;
    use X\Service\Log\Logger\ILogger;
    
    X::system()->log('THIS LOG COMES FROM MAGIC CALL');
    # or
    X::system()->log('THIS IS AN ERROR LOG', ILogger::LV_ERROR);
    

the quick log will use default logger to log conetents.

you are able to use different loggers during the runtime, example : ::

    $service = LogService::getService();
    $logger = $service->getLogger('another-logger');
    $logger->warn('WARNING, RUN !!!');
    $logger->error("THERE IS NO WAY OUT!!!");
    $logger->fatal("I AM  HUNGERING~~~");
    $logger->info("I WANT FOOD");
    $logger->trace("I AM TAKING MY HAND");
    $logger->debug("TRY TO CATCH THAT SELLER");

Database Logger
---------------
database logger use to storage logs in database. you have to create the 
table before using the logger.

- ``logger`` : fixed to ``\X\Service\Log\Logger\Database::class``
- ``dbname`` : the database name that configed in database service
- ``tableName`` : the table name to storage log contents
- ``logLeve`` : level of log
- ``columns`` : define the table columns on inserting log data, the key of item is the name of table column, and the value could be a string or a validated callback handler, if value is a callable value, logger will call it on writing data to database and pass the log content to the handler, otherwise, if the value is a string, logger will treate it as an attribute of log and then get attribute value from the log content.

File Logger
-----------
file logger use to storage logs in file

- ``logger`` : fixed to ``\X\Service\Log\Logger\File::class``
- ``path`` : the log file path
- ``maxSize`` : max file of log file in byte, once the size rich the limiation a new log file will be generated
- ``enableDailyFile`` : if this attribute set to true, the log file will be splited by date. default to false
- ``format`` : the log formate defination , default to '{prettyTime} [{levelName}] {content}', the ``{attribute}`` is the placeholder for log content attribute
- ``logLeve`` : level of log

Socket Logger
-------------
socket logger use to send log contents to target server.

- ``logger`` : fixed to ``\X\Service\Log\Logger\Socket::class``
- ``logLeve`` : level of log
- ``protocol`` :  => protocol of connection, default ot SOL_TCP,
- ``address`` : address of log server
- ``port`` : port of log server
- ``format`` : the log formate defination , default to '{prettyTime} [{levelName}] {content}', the ``{attribute}`` is the placeholder for log content attribute


Syslog Logger
-------------
log contents with syslog

- ``logger`` : fixed to ``\X\Service\Log\Logger\Syslog::class``
- ``logLeve`` : level of log
- ``ident`` : ident name of logger in syslog
- ``facility`` : default to LOG_USER

