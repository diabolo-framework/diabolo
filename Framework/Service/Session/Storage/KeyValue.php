<?php
namespace X\Service\Session\Storage;
use X\Service\KeyValue\Service;
use X\Service\Session\SessionException;
use X\Service\KeyValue\Storage\KeyValueStorage;
class KeyValue extends StorageBase {
    /** @var string */
    protected $storageName = null;
    /** @var int  */
    protected $lifetime = 3600;
    
    /** @var KeyValueStorage */
    private $storage = null;
    /** @var mixed */
    private $oldSessionValue = null;
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::open()
     */
    public function open($save_path, $session_name) {
        $kvService = Service::getService();
        if ( !$kvService->hasStorage($this->storageName) ) {
            throw new SessionException("unable to find key value storage `{$this->storageName}` in key value service");
        }
        $this->storage = $kvService->getStorage($this->storageName);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::close()
     */
    public function close() {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::read()
     */
    public function read($session_id) {
        $value = $this->storage->get($session_id);
        if ( null === $value ) {
            $this->oldSessionValue = null;
            return '';
        }
        $this->oldSessionValue = $value;
        return $value;
    }
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::write()
     */
    public function write($session_id, $session_data) {
        if ( $session_data === $this->oldSessionValue ) {
            return true;
        }
        $this->storage->set($session_id, $session_data, array(
            KeyValueStorage::KEYOPT_EXPIRE_AT => $this->lifetime,
        ));
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::destroy()
     */
    public function destroy($session_id) {
        $this->storage->delete($session_id);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::gc()
     */
    public function gc($maxlifetime) {
        return true;
    }
}