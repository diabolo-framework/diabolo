<?php
namespace X\Service\XSession\Storage;
class Database implements \SessionHandlerInterface {
    /** 配置信息 */
    private $option = null;
    /** \PDO */
    private $pdo = null;
    /** 未修改之前的SESSION值 */
    private $oldSessionValue = null;
    /** 会话扩展数据 */
    private $extSessionData = array();
    
    /**
     * @param unknown $option
     */
    public function __construct( $option ) {
        $this->option = $option;
    }
    
    /**
     * 在服务关闭之前调用
     * @return void 
     */
    public function beforeServiceClose() {
        if ( null !== $this->option['serializeHandler'] ) {
            $this->extSessionData = $this->option['serializeHandler']();
        }
    }
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::open()
     */
    public function open($save_path, $session_name) {
        $this->pdo = new \PDO($this->option['dsn'], $this->option['user'], $this->option['password']);
        $this->pdo->exec("SET NAMES UTF8");
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::close()
     */
    public function close() {
        $this->pdo = null;
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::read()
     */
    public function read($session_id) {
        $query = "SELECT * FROM {$this->option['table']} WHERE ID=:id AND EXPIRED_AT > :now";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $session_id);
        $statement->bindValue(':now', date('Y-m-d H:i:s'));
        if ( !$statement->execute() ) {
            $errorInfo = $statement->errorInfo();
            throw new \Exception("session read error : {$errorInfo[2]}");
        }
        
        $data = $statement->fetch(\PDO::FETCH_ASSOC);
        if ( false === $data ) {
            return '';
        }
        $this->oldSessionValue = $data['RAW'];
        return $data['RAW'];
    }
    
    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::write()
     */
    public function write($session_id, $session_data) {
        if ( empty($session_data) || $this->oldSessionValue === $session_data ) {
            return true;
        }
        
        $data = $this->extSessionData;
        $data['RAW'] = $session_data;
        $data['EXPIRED_AT'] = date('Y-m-d H:i:s');
        
        $attrs = array();
        $cols = array();
        foreach ( $data as $key => $value ) {
            $cols[] = "{$key} = :{$key}";
            $attrs[':'.$key] = $value;
        }
        
        $attrs[':EXPIRED_AT'] = date('Y-m-d H:i:s',time()+$this->option['lifetime']);
        $attrs[':ID'] = $session_id;
        
        $query = "UPDATE {$this->option['table']} SET ".implode(',', $cols).' WHERE ID = :ID';
        $statement = $this->pdo->prepare($query);
        foreach ( $attrs as $key => $value ) {
            $statement->bindValue($key, $value);
        }
        if ( !$statement->execute() ) {
            $errorInfo = $statement->errorInfo();
            throw new \Exception("session write error : {$errorInfo[2]}");
        }
        
        if ( 0 === $statement->rowCount() ) {
            $data['ID'] = $session_id;
            $insert = "INSERT INTO {$this->option['table']} (%s) VALUES (%s)";
            $insert = sprintf($insert, implode(',', array_keys($data)), implode(',', array_keys($attrs)));
            $statement = $this->pdo->prepare($insert);
            foreach ( $attrs as $key => $value ) {
                $statement->bindValue($key, $value);
            }
            if ( !$statement->execute() ) {
                $errorInfo = $statement->errorInfo();
                throw new \Exception("session write error : {$errorInfo[2]}");
            }
        }
        
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::destroy()
     */
    public function destroy($session_id) {
        $query = "DELETE FROM {$this->option['table']} WHERE ID = :id";
        $stat = $this->pdo->prepare($query);
        $stat->bindValue(':id', $session_id);
        $stat->execute();
        return true;
    }

    /**
     * {@inheritDoc}
     * @see SessionHandlerInterface::gc()
     */
    public function gc($maxlifetime) {
        $query = "DELETE FROM {$this->option['table']} WHERE EXPIRED_AT < :now";
        $stat = $this->pdo->prepare($query);
        $stat->bindValue(':now', date('Y-m-d H:i:s'));
        $stat->execute();
        return true;
    }
}