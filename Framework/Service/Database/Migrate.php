<?php
namespace X\Service\Database;
use X\Core\X;
use X\Service\Database\Migration\HistoryHandler\DatabaseMigrationHistoryHandler;
use X\Service\Database\Migration\HistoryHandler\File;
class Migrate {
    /** @var string */
    private $scriptPath = null;
    /** @var DatabaseMigrationHistoryHandler */
    private $history = null;
    /** @var string */
    private $namespace = '';
    
    /**
     * @param array $config
     * <li> path </li>
     * <li> history </li>
     */
    public function __construct( $config ) {
        $this->namespace = $config['namespace'];
        $this->scriptPath = $config['path'];
        
        $historyConfig = $config['history'];
        $historyClass = File::class;
        if ( isset($historyConfig['class']) ) {
            $historyClass = $historyConfig['class'];
        }
        if ( File::class === $historyClass && !isset($historyConfig['path'])) {
            $historyConfig['path'] = $this->scriptPath;
        }
        $history = new $historyClass($historyConfig);
        $this->history = $history;
    }
    
    /**
     * @param number $step
     */
    public function up( $step=0 ) {
        $migrations = $this->getMigrations(SORT_ASC);
        foreach ( $migrations as $name ) {
            if ( $this->history->hasProcessed($name) ) {
                continue;
            }
            if ( 0 >= $step ) {
                break;
            }
            $step --;
            
            $migrationClass = "{$this->namespace}\\{$name}";
            $migration = new $migrationClass();
            $migration->up();
            $this->history->add($name);
        }
        $this->history->save();
    }
    
    /**
     * @param number $step
     */
    public function down( $step=0 ) {
        $migrations = $this->getMigrations(SORT_DESC);
        foreach ( $migrations as $name ) {
            if ( !$this->history->hasProcessed($name) ) {
                continue;
            }
            if ( 0 >= $step ) {
                break;
            }
            $step --;
            
            $migrationClass = "{$this->namespace}\\{$name}";
            $migration = new $migrationClass();
            $migration->down();
            $this->history->drop($name);
        }
        $this->history->save();
    }
    
    /** @return array */
    private function getMigrations( $sort ) {
        $files = scandir($this->scriptPath);
        if ( SORT_DESC === $sort ) {
            $files = array_reverse($files);
        }
        
        foreach ( $files as $index => $file ) {
            if ( '.' === $file[0] ) {
                unset($files[$index]);
                continue;
            }
            
            require_once $this->scriptPath.DIRECTORY_SEPARATOR.$file;
            $files[$index] = substr($file, strpos($file,'.')+1);
            $files[$index] = substr($files[$index], 0, strrpos($files[$index],'.'));
        }
        return array_values($files);
    }
}