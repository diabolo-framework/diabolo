<?php
use X\Service\Database\Service as DatabaseService;
use X\Service\Database\Driver\Mysql;
return array(
'document_root' => __DIR__,
'module_path' => array(),
'service_path' => array(),
'library_path' => array(),
'modules' => array(),
'params' => array(
    'MysqlDriverConfig' => array(
        'driver' => Mysql::class,
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'UTF8',
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
    'MSsqlDriverConfig' => array(
        'host' => '39.104.28.34',
        'username' => 'sa',
        'password' => 'websoft9!',
        'dbname' => 'dbtester',
        'port' => 1433
    ),
    'OracleDriverConfig' => array(
        'host' => '127.0.0.1',
        'username' => 'c##bighero',
        'password' => 'bighero',
        'serviceName' => 'ORCL',
        'port' => 1521
    ),
    'FirebirdDriverConfig' => array(
        'host' => '39.104.28.34',
        'username' => 'SYSDBA',
        'password' => 'ginhappy',
        'dbname' => 'C:\\Program Files\\Firebird\\Data\\TEST1.FDB',
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