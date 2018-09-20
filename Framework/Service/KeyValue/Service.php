<?php
namespace X\Service\KeyValue;
use X\Core\X;
use X\Core\Service\XService;
use X\Service\KeyValue\Storage\StorageBase;
/**
 * The service class
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Service extends XService {
    /** @var array */
    protected $storages = array();
    /** @var StorageBase[] */
    private $storageInstance = array();
    
    /**
     * @param unknown $name
     * @throws KeyValueException
     * @return \X\Service\KeyValue\Storage\StorageBase
     */
    public function getStorage( $name ) {
        if ( !isset($this->storages[$name]) ) {
            throw new KeyValueException("can not find storage `{$name}`.");
        }
        if ( !isset($this->storageInstance[$name]) ) {
            $storageClass = $this->storages[$name]['class'];
            $this->storageInstance[$name] = new $storageClass($this->storages[$name]);
        }
        return $this->storageInstance[$name];
    }
}