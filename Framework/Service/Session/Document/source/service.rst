Service
=======
session service use to manage session value and storages.

Configuration
-------------
example : ::

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
    
    # database service use to support session storage
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
    
    # key value service use to support session storage
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

Storage
-------
session service support following storages, if you do not config the ``storage``, 
the default php session storage you configed in php.ini will be used.

- ``X\Service\Session\Storage\Database::class`` :

the database storage use to sstore session data in any database that database service 
supported, like sqlite, mysql. and so on.

    * ``dbname`` the name of database configed in database service
    * ``tableName`` name of table to store session data
    * ``lifetime`` seconds of lifetime of session data, default to 3600

- ``X\Service\Session\Storage\KeyValue::class`` :

key value storage use to store session data in key/value database, such as redis, memcached, ...

    * ``storageName`` name of kv storage configed in keyvalue service
    * ``lifetime`` seconds of lifetime of session data, default to 3600

Read/Write Session
------------------
example : ::

    # start session
    $service->startSession();
    
    # set value to session
    $service->set('TEST_02', "TV02");
    
    # read value from session
    $service->get('TEST_02')
    
    # close session
    $service->closeSession();

Session Flash
-------------
flash use to get value form session, but only able to read it once.
exampel ::

    use X\Service\Session\SessionFlash;
    SessionFlash::set('TEST001', 'VAL001');
        
    # not TEST001 exists
    $this->assertTrue(SessionFlash::has('TEST001'));
     
    # get value of TEST001
    $this->assertEquals('VAL001', SessionFlash::get('TEST001'));
       
    # not TEST001 does not exists
    $this->assertFalse(SessionFlash::has('TEST001'));
