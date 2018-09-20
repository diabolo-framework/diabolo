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
                'kvdbname' => array(
                    'driver' => \X\Service\Database\Driver\Sqlite::class,
                    'path' => __DIR__.'/Resource/Data/kvtest.db',
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
                'strDB' => array(
                    'class' => \X\Service\KeyValue\Storage\Database::class,
                    'dbname' => 'kvdbname',
                    'tablename' => 'kvtablename',
                    'keyname' => 'key',
                    'valuename' => 'value',
                    'metaname' => 'meta',
                ),
                'strRedis' => array(
                    'class' => \X\Service\KeyValue\Storage\Redis::class,
                    'host' => 'kvdbname',
                    'post' => 'kvtablename',
                    'username' => 'key',
                    'password' => 'value',
                    'db' => '0',
                ),
                'strMemcached' => array(
                    'class' => \X\Service\KeyValue\Storage\Memcached::class,
                    'host' => 'kvdbname',
                    'post' => 'kvtablename',
                ),
            ),
        ),
    ),
),
);