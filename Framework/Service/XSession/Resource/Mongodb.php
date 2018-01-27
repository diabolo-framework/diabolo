<?php 
namespace MongoDB\BSON {
    interface Serializable {}
}
namespace MongoDB\Driver {
    class ReadPreference {}
    class Command {}
    
    class Cursor implements \Traversable {
        final private function __construct ( ) {}
        /** @return \MongoDB\Driver\CursorId */
        final public function getId ( ) {}
        /** @return \MongoDB\Driver\Server */
        final public function getServer ( ) {}
        /** @return boolean */
        final public function isDead ( ) {}
        /** @return void */
        final public function setTypeMap ( array $typemap ) {}
        /** @return array */
        final public function toArray ( ) {}
    }
    
    class Query {
        final public function __construct ( $filter,$queryOptions=[] ) {}
    }
        
    class Server {
        /* Constants */
        const TYPE_UNKNOWN = 0 ;
        const TYPE_STANDALONE = 1 ;
        const TYPE_MONGOS = 2 ;
        const TYPE_POSSIBLE_PRIMARY = 3 ;
        const TYPE_RS_PRIMARY = 4 ;
        const TYPE_RS_SECONDARY = 5 ;
        const TYPE_RS_ARBITER = 6 ;
        const TYPE_RS_OTHER = 7 ;
        const TYPE_RS_GHOST = 8 ;
        
        /* Methods */
        final private function __construct () {}
        /** @return \MongoDB\Driver\WriteResult */
        final public function executeBulkWrite ( $namespace , BulkWrite $bulk , WriteConcern $writeConcern=null ) {}
        /** @return \MongoDB\Driver\Cursor */
        final public function executeCommand ( $db , Command $command , ReadPreference $readPreference=null ) {}
        /** @return \MongoDB\Driver\Cursor */
        final public function executeQuery ( $namespace , Query $query, ReadPreference $readPreference=null ) {}
        /** @return string */
        final public function getHost ( ) {}
        /** @return array */
        final public function getInfo ( ) {}
        /** @return string  */
        final public function getLatency ( ) {}
        /** @return integer  */
        final public function getPort ( ) {}
        /** @return array  */
        final public function getTags ( ) {}
        /** @return integer  */
        final public function getType ( ) {}
        /** @return boolean  */
        final public function isArbiter ( ) {}
        /** @return boolean */
        final public function isHidden ( ) {}
        /** @return boolean  */
        final public function isPassive ( ) {}
        /** @return boolean  */
        final public function isPrimary ( ) {}
        /** @return boolean */
        final public function isSecondary ( ) {}
    }
    
    class BulkWrite implements \Countable {
        /* Methods */
        public function __construct (array $options=array() ) {}
        public function count () {}
        public function delete ( $filter, array $deleteOptions=[] ) {}
        public function insert ($document ) {}
        public function update ( $filter , $newObj, array $updateOptions=[] ) {}
    }
    
    class WriteConcern implements \MongoDB\BSON\Serializable {
        /* Constants */
        const MAJORITY = "majority" ;
        /* Methods */
        final public function __construct ( $w, $wtimeout=null, $journal=null ) {}
        /** @return object */
        final public function bsonSerialize ( ) {}
        /** @return boolean|null */
        final public function getJournal ( ) {}
        /** @return string|integer|null */
        final public function getW ( ) {}
        /** @return integer */
        final public function getWtimeout ( ) {}
        /** @return boolean */
        final public function isDefault ( ) {}
    }
        
    class Manager {
        final public function __construct ($uri = "mongodb://127.0.0.1/", array $uriOptions = [], array $driverOptions = [] ) {}
        /** @return \MongoDB\Driver\WriteResult */
        final public function executeBulkWrite ( $namespace , BulkWrite $bulk , WriteConcern $writeConcern=null ) {}
        /** @return \MongoDB\Driver\Cursor */
        final public function executeCommand ( $db , Command $command, ReadPreference $readPreference=null ) {}
        /** @return \MongoDB\Driver\Cursor */
        final public function executeQuery ( $namespace , Query $query, ReadPreference $readPreference = null ){}
        /** @return \MongoDB\Driver\ReadConcern */
        final public function getReadConcern (  ){}
        /** @return \MongoDB\Driver\ReadPreference */
        final public function getReadPreference (  ){}
        /** @return array */
        final public function getServers (  ){}
        /** @return \MongoDB\Driver\WriteConcern */
        final public function getWriteConcern (  ){}
        /** @return \MongoDB\Driver\Server */
        final public function selectServer ( ReadPreference $readPreference ){}
    }
}