Service
=======

Configuration
-------------
service config : ::

    use X\Service\Database\Service as DatabaseService;
    use X\Service\Database\Driver\Mysql;
    use X\Service\Database\Driver\Sqlite;
    use X\Service\Database\Driver\Postgresql;
    use X\Service\Database\Driver\Oracle;
    use X\Service\Database\Driver\Mssql;
    use X\Service\Database\Driver\Firebird;

    return array(
    'services' => array(
        'Database' => array(
            'class' => DatabaseService::class,
                'enable' => true,
                'delay' => true,
                'params' => array(
                    'commandPaths' => array(
                        '/Paht/To/Command',
                        '/Another/Paht/To/Command'
                     ),
                     'databases' => array(
                         'default' => array(
                             'driver' => Sqlite::class,
                             'path' => __DIR__.'/Resource/Data/demo.db',
                         ),
                        'mysqlTestDB' => array(
                            'driver' => Mysql::class,
                            'host' => '127.0.0.1',
                            'username' => 'root',
                            'password' => '',
                            'charset' => 'UTF8',
                            'dbname' => 'diabolo.test',
                        ),
                        'sqliteTestDB' => array(
                            'driver' => Sqlite::class,
                            'path' => __DIR__.'/Resource/Data/diabolo.db',
                        ),
                        'postgresqlTestDB' => array(
                            'driver' => Postgresql::class,
                            'host' => '106.12.16.95',
                            'username' => 'postgres',
                            'password' => 'ginhappy',
                            'dbname' => 'diabolo.test',
                            'port' => 5432
                        ),
                        'oracleTestDB' => array(
                            'driver' => Oracle::class,
                            'host' => '192.168.1.102',
                            'username' => 'C##DIABOLO',
                            'password' => 'C##DIABOLO',
                            'serviceName' => 'ORCL',
                            'port' => 1521
                        ),
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
                            'host' => '192.168.1.105',
                            'username' => 'SYSDBA',
                            'password' => '123456789',
                            'dbname' => 'D:\\DIABOLO.FDB',
                        ),
                    ),
                ),
            ),
        ),
    );

Basic Usage
-----------
examples : ::

    use X\Service\Database\Service;
    $dbService = Service::getService();
    
    $dbname = 'firebirdTestDB';
    
    # get database instance
    $db = $dbService->getDB($dbname);
    
    # check if database available
    if ( $dbService->hasDB($dbname) ) {
        echo "database {$dbname} exists.";
    } 
    
    # reload database 
    $dbService->reloadDB($dbname);
    
