<?php
use X\Service\Database\Service as DatabaseService;
use X\Service\Database\Driver\Mysql;
use X\Service\Database\Driver\Sqlite;
use X\Service\Database\Driver\Postgresql;
use X\Service\Database\Driver\Oracle;
use X\Service\Database\Driver\Mssql;
use X\Service\Database\Driver\Firebird;

return array(
'document_root' => __DIR__,
'module_path' => array(),
'service_path' => array(),
'library_path' => array(),
'modules' => array(),
'params' => array(),
'services' => array(
    'Database' => array(
        'class' => DatabaseService::class,
        'enable' => true,
        'delay' => true,
        'params' => array(
            'databases' => array(
//                 'mysqlTestDB' => array(
//                     'driver' => Mysql::class,
//                     'host' => '127.0.0.1',
//                     'username' => 'root',
//                     'password' => '',
//                     'charset' => 'UTF8',
//                     'dbname' => 'diabolo.test',
//                 ),
//                 'sqliteTestDB' => array(
//                     'driver' => Sqlite::class,
//                     'path' => __DIR__.'/Resource/Data/diabolo.db',
//                 ),
//                 'postgresqlTestDB' => array(
//                     'driver' => Postgresql::class,
//                     'host' => '106.12.16.95',
//                     'username' => 'postgres',
//                     'password' => 'ginhappy',
//                     'dbname' => 'diabolo.test',
//                     'port' => 5432
//                 ),
//                 'oracleTestDB' => array(
//                     'driver' => Oracle::class,
//                     'host' => '192.168.1.102',
//                     'username' => 'C##DIABOLO',
//                     'password' => 'C##DIABOLO',
//                     'serviceName' => 'ORCL',
//                     'port' => 1521
//                 ),
                'mssqlTestDB' => array(
                    'driver' => Mssql::class,
                    'host' => '192.168.1.104',
                    'username' => 'diabolo',
                    'password' => 'diabolo',
                    'dbname' => 'diabolo',
                    'port' => 1433
                ),
                'firebirdTestDB' => array(
                    'driver' => Firebird::class,
                    'host' => '192.168.1.103',
                    'username' => 'SYSDBA',
                    'password' => '123456789',
                    'dbname' => 'D:\\DIABOLO.FDB',
                ),
            ),
        ),
    ),
),
);