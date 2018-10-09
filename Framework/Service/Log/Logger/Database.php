<?php
namespace X\Service\Log\Logger;
use X\Service\Database\Query;
use X\Service\Log\LogException;
class Database extends ALogger {
    /**
     * name of database configured in database service.
     * @var string
     */
    protected $dbname = null;
    /**
     * name of table to storage contents
     * @var string
     */
    protected $tableName = null;
    /**
     * table column definations, the key in array should be the name of 
     * table column, and the value will be the content of the log attributes,
     * such as content, log time, or something eles, or , you can set custom 
     * callback to value to generate custome value.
     * @example
     * <pre>
     * array(
     *   'CONTNET' => 'content',
     *   'LEVEL' => 'level',
     *   'LOGGED_AT' => 'loggedAt',
     *   'IP_ADDR' => 'serverIp',
     *   'USER_NAME' => function ( \X\Service\Log\LogContent $log ) {return 'DEMO-USER';},
     *   'USER_ACTION' => array('ANOTHER','CALLBACK'),
     * )
     * </pre>
     * @var array
     */
    protected $columns = array();
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::sync()
     */
    public function sync() {
        $logs = $this->getActiveLogs();
        
        $data = array();
        foreach ( $logs as $log ) {
            if ( $log->isSynced ) {
                continue;
            }
            
            $row = array();
            foreach ( $this->columns as $column => $defination ) {
                $value = null;
                if ( is_callable($defination) ) {
                    $value = $defination($log);
                } else {
                    $value = $log->getAttributeByName($defination);
                }
                $row[$column] = $value;
            }
            $data[] = $row;
            $log->isSynced = true;
        }
        
        $rowCount = Query::insert($this->dbname)->table($this->tableName)->values($data)->exec();
        if ( $rowCount !== count($data) ) {
            throw new LogException('failed to sync log data to database');
        }
    }
}