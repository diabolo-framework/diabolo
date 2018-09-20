Service
=======
key value service use to manage key value storages.

Configuration
-------------
example : ::

    'services' => array(
        # we config database service to support database key-value storage
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
                        'prefix' => 'diabolo_',
                    ),
                    'strRedis' => array(
                        'class' => \X\Service\KeyValue\Storage\Redis::class,
                        'host' => '127.0.0.1',
                        'port' => '6379',
                        'dbindex' => 1,
                        'prefix' => 'diabolo_',
                        'password' => 'diabolo',
                    ),
                    'strMemcached' => array(
                        'class' => \X\Service\KeyValue\Storage\Memcached::class,
                        'host' => '127.0.0.1',
                        'port' => 11211,
                        'prefix' => 'DIABOLO_',
                    ),
                ),
            ),
        ),
    ),

Basic Usage
-----------
example : ::

    use \X\Service\KeyValue\Service;
    $service = Service::getService();
    $storage = $service->getStorage('strDB');

Redis
-----
configuration 

- ``class`` fixed value ``\X\Service\KeyValue\Storage\Redis::class`` for name of redis storage
- ``host`` host address of redis server
- ``port`` port of redis server, default to 6379
- ``dbindex`` db index number, default to 0
- ``prefix`` prefix string of keys, default to empty string
- ``password`` password to redis server, default to null

Memcached
---------
configuration 

- ``class`` fixed value ``\X\Service\KeyValue\Storage\Memcached::class`` for name of memcached straoge
- ``host`` host address of memcached server
- ``port`` port of memcahced server, default to 11211
- ``prefix`` prefix string for keys, default to empty string

also, memcached support to config mutil servers with param ``servers``, here is an example : ::

    'servers' => array(
        #     hostname            port     weight
        array('mem1.domain0.com', 11211,   33),
        array('mem1.domain1.com', 11211,   100),
        array('mem1.domain2.com', 11211,   22),
    ),

if servers configed, the host config item will not working any more.

Database
--------
configuration

- ``dbname`` name of database config in database service
- ``tableName`` name of table to storage data
- ``prefix`` prefix string to keys, default to empty string

