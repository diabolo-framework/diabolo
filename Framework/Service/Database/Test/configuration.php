<?php
use X\Service\Database\Service as DatabaseService;
return array(
'document_root' => __DIR__,
'module_path' => array(),
'service_path' => array(),
'library_path' => array(),
'modules' => array(),
'params' => array(
    'MysqlDriverConfig' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'UTF-8',
        'dbname' => 'diabolo.test',
    ),
    'SqliteDriverConfig' => array(
        'path' => '/home/michael/diabolo.db',
    ),
    'PostgresqlDriverConfig' => array(
        'host' => '127.0.0.1',
        'username' => 'michael',
        'password' => '',
        'dbname' => 'test',
        'port' => 5432
    ),
),
'services' => array(
    'Database' => array(
        'class' => DatabaseService::class,
        'enable' => true,
        'delay' => true,
        'params' => array(),
    ),
),
);