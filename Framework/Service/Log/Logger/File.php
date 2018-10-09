<?php
namespace X\Service\Log\Logger;
use X\Service\Log\LogException;
class File extends AStreamLogger {
    /** 
     * path of log file
     * @var string 
     * */
    protected $path = null;
    /**
     * max size of log file, default to null, means no limitation
     * @var integer
     */
    protected $maxSize = null;
    /**
     * create log file every day with differnet files.
     * @var boolean
     */
    protected $enableDailyFile = false;
    
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
            $data[] = $this->buildLogRow($log);
            $log->isSynced = true;
        }
        $data = implode("\n", $data)."\n";
        
        $this->renameLogFileOnOverSize(strlen($data));
        $logFile = $this->getLogFileName();
        $writeResult = file_put_contents($logFile, $data, FILE_APPEND);
        if ( false === $writeResult ) {
            throw new LogException("failed to write log data into `{$logFile}`");
        }
    }
    
    /**
     * check file size and rename it if oversize
     * @return void
     */
    private function renameLogFileOnOverSize( $appendSize ) {
        $logFile = $this->getLogFileName();
        if ( empty($this->maxSize) || !file_exists($logFile) ) {
            return;
        }
        if ( filesize($logFile)+$appendSize < $this->maxSize ) {
            return;
        }
        
        $files = glob($logFile.'-*');
        $newFile = $logFile.'-'.(count($files)+1);
        rename($logFile, $newFile);
    }
    
    /**
     * generate log file name
     * @return string
     */
    private function getLogFileName() {
        if ( !$this->enableDailyFile ) {
            return $this->path;
        }
        return $this->path.'-'.date('Ymd');
    }
}