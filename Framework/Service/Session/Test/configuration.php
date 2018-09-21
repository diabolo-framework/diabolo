<?php
return array(
'document_root' => __DIR__,
'module_path' => array(),
'service_path' => array(),
'library_path' => array(),
'modules' => array(
    'Sport' => array(),
    'User' => array(),
),
'params' => array(),
'services' => array(
    'Session' => array(
        'class' => \X\Service\Session\Service::class,
        'enable' => true,
        'delay' => false,
        'params' => array(
            'holders' => array('cookie', 'get', 'post', 'request'),
            'storage' => array(
                'class' => X\Service\Session\Storage\Database::class,
                'dbname' => 'seesionStorage',
                'tableName' => 'demo_sessions',
                'lifetime' => 10,
            ),
        ),
    ),
    
    'Database' => array(
        'class' => \X\Service\Database\Service::class,
        'enable' => true,
        'delay' => true,
        'params' => array(
            'databases' => array(
                'seesionStorage' => array(
                    'driver' => \X\Service\Database\Driver\Sqlite::class,
                    'path' => __DIR__.'/Resource/Data/diabolo.db',
                ),
            ),
        ),
    ),
    'KeyValue' => array(
        'class' => \X\Service\KeyValue\Service::class,
        'enable' => true,
        'delay' => true,
        'params' => array(
            'storages' => array(
                'sesionStorage' => array(
                    'class' => \X\Service\KeyValue\Storage\Redis::class,
                    'host' => '127.0.0.1',
                    'post' => 6379,
                    'dbindex' => 1,
                    'prefix' => 'DIB_SSN_',
                ),
            ),
        ),
    ),
),
);