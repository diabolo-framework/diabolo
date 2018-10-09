<?php
return array(
'document_root' => __DIR__,
'module_path' => array(),
'service_path' => array(),
'library_path' => array(),
'modules' => array(),
'params' => array(),
'services' => array(
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
),
);