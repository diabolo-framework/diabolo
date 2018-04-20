<?php 
namespace X\Service\XDatabase\Core\SQL;
use X\Service\XDatabase\Core\Database\Database;
use X\Service\XDatabase\Service as XDatabaseService;
class SqlLoader {
    /** @var array */
    private static $cachedSqlInfo = array();
    /** @var string */
    private $sqlFilePath = null;
    /** @var Database */
    private $database = null;
    /** @var array */
    private $sqlInfo = null;
    /** @var string */
    private $sqlString = null;
    
    /** @return self */
    public static function load($path, $db=null) {
        $loader = new SqlLoader();
        $loader->sqlFilePath = $path;
        if ( is_string($db) ) {
            $loader->database = XDatabaseService::getService()->get($db);
        } else {
            $loader->database = $db;
        }
        if ( isset(self::$cachedSqlInfo[$path]) ) {
            $loader->sqlInfo = self::$cachedSqlInfo[$path];
        }
        return $loader;
    }
    
    /** @return string */
    public function toString() {
        if ( null === $this->sqlInfo ) {
            $this->sqlInfo['template'] = file_get_contents($this->sqlFilePath);
            self::$cachedSqlInfo[$this->sqlFilePath] = $this->sqlInfo;
        }
        $sql = $this->sqlInfo['template'];
        $this->sqlString = $sql;
        return $this->sqlString;
    }
    
    /** 
     * Return the number of effected rows.
     * @return int 
     * */
    public function exec() {
        return $this->database->exec($this->toString());
    }
}