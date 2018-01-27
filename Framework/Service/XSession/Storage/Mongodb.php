<?php 
namespace X\Service\XSession\Storage;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;

class Mongodb implements \SessionHandlerInterface {
    /** @var array*/
    private $option = null;
    /** @var \MongoDB\Driver\Manager */
    private $manager = null;
    /** @var \MongoDB\Driver\Server */
    private $server = null;
    /** */
    private $oldSessionData = null;
    /** */
    private $documentId = null;
    
    /**
     * @param unknown $option
     */
    public function __construct( $option ) {
        $this->option = $option;
    }
    
    /**
     * @return boolean
     */
    public function beforeServiceClose() {
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::open()
     */
    public function open($save_path, $session_name) {
        $manager = new Manager($this->option['uri']);
        $server = $manager->selectServer($manager->getReadPreference());
        
        $this->manager = $manager;
        $this->server = $server;
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::close()
     */
    public function close() {
        $this->manager = null;
        $this->server = null;
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::read()
     */
    public function read($session_id) {
        $query = new Query(array('sid'=>$session_id));
        $namespace = "{$this->option['database']}.{$this->option['collection']}";
        $cursor = $this->server->executeQuery($namespace, $query);
        $cursor->setTypeMap(['root' =>'array','document'=>'array','array'=>'array']);
        
        $data = $cursor->toArray();
        if ( empty($data) ) {
            return '';
        }
        $this->oldSessionData = $data[0]['raw'];
        $this->documentId = $data[0]['_id'];
        return $data[0]['raw'];
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::write()
     */
    public function write($session_id, $session_data) {
        if ( $this->oldSessionData === $session_data ) {
            return true;
        }
        
        $document = array(
            'sid' => $session_id,
            'raw' => $session_data,
            'data' => $_SESSION,
            'expired_at'=>time()+$this->option['lifetime'],
        );
        
        $bulk = new BulkWrite();
        if ( null === $this->documentId ) {
            $insertedId = $bulk->insert($document);
        } else {
            $bulk->update(array('sid'=>$session_id), $document);
        }
        
        $namespace = "{$this->option['database']}.{$this->option['collection']}";
        $this->server->executeBulkWrite($namespace, $bulk);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::destroy()
     */
    public function destroy($session_id) {
        $deleteOptions = array('limit' => 1);
        $bulk = new BulkWrite();
        $bulk->delete(array('sid'=>$session_id), $deleteOptions);
        $namespace = "{$this->option['database']}.{$this->option['collection']}";
        $this->server->executeBulkWrite($namespace, $bulk);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::gc()
     */
    public function gc($maxlifetime) {
        $bulk = new BulkWrite();
        $bulk->delete(array('expired_at'=>array('$lte'=>time())));
        $namespace = "{$this->option['database']}.{$this->option['collection']}";
        $this->server->executeBulkWrite($namespace, $bulk);
        return true;
    }
}