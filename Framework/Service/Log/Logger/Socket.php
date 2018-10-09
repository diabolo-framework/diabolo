<?php
namespace X\Service\Log\Logger;
use X\Service\Log\LogException;

class Socket extends AStreamLogger {
    /**
     * @var integer
     */
    protected $protocol = SOL_TCP;
    /**
     * @var string
     */
    protected $address = null;
    /**
     * @var integer
     */
    protected $port = null;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Log\Logger\ILogger::sync()
     */
    public function sync() {
        $socket = socket_create(AF_INET, SOCK_STREAM, $this->protocol);
        if ( false === socket_connect($socket, $this->address, $this->port) ) {
            throw new LogException("failed to connect to `{$this->address}:{$this->port}` : ".socket_strerror(socket_last_error()));
        }
        
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
        if ( false === socket_write($socket, $data) ) {
            throw new LogException('failed to write content to socket : '.socket_strerror(socket_last_error()));
        }
        socket_close($socket);
    }
}