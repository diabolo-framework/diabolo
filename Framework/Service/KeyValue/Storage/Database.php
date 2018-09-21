<?php
namespace X\Service\KeyValue\Storage;
use X\Service\Database\Database as XDatabase;
use X\Service\Database\Service as DatabaseService;
use X\Service\Database\Query;
use X\Service\Database\Table;
use X\Service\Database\Table\Column;
use X\Service\Database\Query\Condition;
class Database extends StorageBase {
    /** @var string */
    protected $dbname = null;
    /** @var string */
    protected $tableName = null;
    /** @var string */
    protected $prefix = '';
    
    /** @var XDatabase */
    private $database = null;
    /** @var array */
    private $kvCached = array();
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::init()
     */
    protected function init() {
        parent::init();
        $this->database = DatabaseService::getService()->getDB($this->dbname);
        if ( !Table::exists($this->database, $this->tableName) ) {
            $this->createStorageTable();
        }
    }
    
    /** @return void */
    private function createStorageTable() {
        $key = Column::build()
            ->setName('key')
            ->setIsNotNull(true)
            ->setIsPrimary(true)
            ->setType(Column::T_STRING)
            ->setLength(256);
        
        $value = Column::build()->setName('value')->setType(Column::T_STRING)->setLength(4096);
        $option = Column::build()->setName('expired')->setType(Column::T_INTEGER);
        $table = Table::create($this->database, $this->tableName, array($key, $value, $option));
        $table->addIndex('key_idx', array('key'));
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::get()
     */
    public function get($key) {
        $key = $this->prefix.$key;
        
        if ( array_key_exists($key, $this->kvCached) ) {
            $data = $this->kvCached[$key];
        } else {
            $data = Query::select($this->database)
                ->from($this->tableName)
                ->where(['key' => $key])
                ->one();
        }
        if ( (null!==$data['expired']) && (time()>$data['expired']) ) {
            return null;
        }
        
        $this->kvCached[$key] = $data;
        if ( false === $data ) {
            return null;
        }
        return $data['value'];
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::set()
     */
    public function set($key, $value, $option = array()) {
        $key = $this->prefix.$key;
        
        $condition = ['key'=>$key];
        $value = ['value' => $value];
        if ( isset($option[self::KEYOPT_EXPIRE_AT]) ) {
            $value['expired'] = time() + $option[self::KEYOPT_EXPIRE_AT];
        }
        
        $count = Query::select($this->database)->from($this->tableName)->where($condition)->count();
        if ( 0 !== $count ) {
            $rowCount = Query::update($this->database)->table($this->tableName)->values($value)->where($condition)->exec();
        } else {
            $value['key'] = $key;
            $rowCount = Query::insert($this->database)->table($this->tableName)->value($value)->exec();
        }
        
        if ( array_key_exists($key, $this->kvCached) ) {
            unset($this->kvCached[$key]);
        }
        return 1 === $rowCount;
    }
    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::clean()
     */
    public function clean() {
        Query::truncateTable($this->database)->table($this->tableName)->exec();
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::match()
     */
    public function match($pattern) {
        $keys = Query::select($this->database)
            ->column('key')
            ->from($this->tableName)
            ->where(Condition::build()->like('key',$this->prefix.$pattern.'%'))
            ->all()
            ->fetchAll();
        foreach ( $keys as $index => $value ) {
            $keys[$index] = $value['key'];
        }
        return $keys;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::rename()
     */
    public function rename($key, $newKey){
        Query::update($this->database)
            ->table($this->tableName)
            ->set('key', $this->prefix.$newKey)
            ->where(['key'=>$this->prefix.$key])
            ->exec();
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::delete()
     */
    public function delete($key) {
        Query::delete($this->database)
            ->table($this->tableName)
            ->where(['key'=>$this->prefix.$key])
            ->exec();
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::exists()
     */
    public function exists($key) {
        $count = Query::select($this->database)
            ->from($this->tableName)
            ->where(['key'=>$this->prefix.$key])
            ->count();
        return 1 === $count;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\KeyValue\Storage\StorageBase::size()
     */
    public function size(){
        $count = Query::select($this->database)
            ->from($this->tableName)
            ->count();
        return $count;
    }
}