<?php
namespace X\Service\Database\Migration\HistoryHandler;
class File implements DatabaseMigrationHistoryHandler {
    /** @var string */
    private $path = null;
    /** @var array */
    private $history = array();
    
    /**
     * @param unknown $config
     */
    public function __construct( $config ) {
        if ( !is_dir($config['path']) ) {
            $this->path = $config['path'];
        } else {
            $this->path = rtrim($config['path'], DIRECTORY_SEPARATOR."/")."/.history.php";
        }
        if ( file_exists($this->path) ) {
            $this->history = require $this->path;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\HistoryHandler\DatabaseMigrationHistoryHandler::save()
     */
    public function save() {
        $history = var_export($this->history, true);
        $content = "<?php \nreturn {$history}; ";
        file_put_contents($this->path, $content);
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\HistoryHandler\DatabaseMigrationHistoryHandler::hasProcessed()
     */
    public function hasProcessed($name) {
        return in_array($name, $this->history);
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\HistoryHandler\DatabaseMigrationHistoryHandler::add()
     */
    public function add($name){
        $this->history[] = $name;
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\HistoryHandler\DatabaseMigrationHistoryHandler::drop()
     */
    public function drop($name) {
        unset($this->history[array_search($name, $this->history)]);
        $this->history = array_values($this->history);
    }
}
