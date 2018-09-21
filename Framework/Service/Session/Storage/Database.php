<?php
namespace X\Service\Session\Storage;
use X\Service\Database\Database as XDatabase;
use X\Service\Database\Service as DatabaseService;
use X\Service\Session\SessionException;
use X\Service\Database\Query;
use X\Service\Database\Query\Condition;
use X\Service\Database\Table;
use X\Service\Database\Table\Column;
class Database extends StorageBase {
    /** @var string */
    protected $dbname = null;
    /** @var string */
    protected $tableName = null;
    /** @var string */
    protected $lifetime = 3600;
    
    /** @var XDatabase */
    private $database = null;
    /** @var string */
    private $oldSessionValue = null;
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::open()
     */
    public function open($save_path, $session_name) {
        $dbService = DatabaseService::getService();
        if ( !$dbService->hasDB($this->dbname) ) {
            throw new SessionException("unable to find database storage `{$this->dbname}` in database service");
        }
        $this->database = $dbService->getDB($this->dbname);
        if ( !Table::exists($this->database, $this->tableName) ) {
            $this->createSessionTable();
        }
        
        return true;
    }
    
    /** */
    private function createSessionTable() {
        $id = Column::build()->setName('id')->setIsPrimary(true)->setType(Column::T_STRING)->setLength(64)->setIsNotNull(true);
        $value = Column::build()->setName('value')->setType(Column::T_STRING)->setLength(4096);
        $expired = Column::build()->setName('expired')->setType(Column::T_INTEGER);
        
        Table::create($this->database, $this->tableName, array($id, $value, $expired));
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
        $sessionData = Query::select($this->database)
            ->from($this->tableName)
            ->where(['id'=>$session_id])
            ->one();
        if ( false === $sessionData ) {
            $this->oldSessionValue = null;
            return '';
        }
            
        if ( time() > $sessionData['expired'] ) {
            $sessionData = null;
            Query::delete($this->database)->from($this->tableName)->where(['id'=>$session_id])->exec();
            return '';
        }
        $this->oldSessionValue = $sessionData['value'];
        return $sessionData['value'];
    }
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::write()
     */
    public function write($session_id, $session_data) {
        if ( empty($session_data) || $this->oldSessionValue === $session_data ) {
            return true;
        }
        
        $data = array();
        $data['id'] = $session_id;
        $data['value'] = $session_data;
        $data['expired'] = time() + $this->lifetime;
        if ( null === $this->oldSessionValue ) {
            Query::insert($this->database)
                ->table($this->tableName)
                ->value($data)
                ->exec();
        } else {
            Query::update($this->tableName)
                ->table($this->tableName)
                ->values($data)
                ->where(['id'=>$session_id])
                ->exec();
        }
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::destroy()
     */
    public function destroy($session_id) {
        Query::delete($this->database)
            ->from($this->tableName)
            ->where(['id'=>$session_id])
            ->exec();
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::gc()
     */
    public function gc($maxlifetime) {
        Query::delete($this->database)
            ->from($this->tableName)
            ->where(Condition::build()->lessThan('expired', time()))
            ->exec();
        return true;
    }
}