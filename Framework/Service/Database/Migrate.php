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
    /** */
    private $processHandler = null;
    
    /**
     * @param array $config
     * <li> path </li>
     * <li> history </li>
     * <li> processHandler </li>
     */
    public function __construct( $config ) {
        $this->namespace = $config['namespace'];
        $this->scriptPath = $config['scriptPath'];
        $this->processHandler = $config['processHandler'];
        
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
        $counter = 0;
        $migrations = $this->getMigrations(SORT_ASC);
        
        $stepCounter = (0===$step) ? 1 : $step;
        foreach ( $migrations as $name ) {
            if ( $this->history->hasProcessed($name) ) {
                continue;
            }
            if ( 0 !== $step ) {
                if ( 0 >= $stepCounter ) {
                    break;
                }
                $stepCounter --;
            }
            
            $counter ++;
            
            $migrationClass = "{$this->namespace}\\{$name}";
            $migration = new $migrationClass();
            $migration->setProcessHandler(array($this, 'processHandler'));
            $this->processHandler('StartMigration',array('name'=>$name));
            try {
                $migration->up();
                $this->history->add($name);
                $this->history->save();
            } catch ( \Exception $e ) {
                $this->processHandler('Error',array('message'=>$e->getMessage(), 'file'=>$e->getFile(), 'line'=>$e->getLine()));
            }
            
        }
        $this->processHandler('DoneMigration',array('count'=>$counter));
    }
    
    /**
     * @param number $step
     */
    public function down( $step=0 ) {
        $counter = 0;
        $migrations = $this->getMigrations(SORT_DESC);
        
        $stepCounter = (0===$step) ? 1 : $step;
        foreach ( $migrations as $name ) {
            if ( !$this->history->hasProcessed($name) ) {
                continue;
            }
            if ( 0 !== $step ) {
                if ( 0 >= $stepCounter ) {
                    break;
                }
                $stepCounter --;
            }
            
            $counter ++;
            
            $migrationClass = "{$this->namespace}\\{$name}";
            $migration = new $migrationClass();
            $migration->setProcessHandler(array($this, 'processHandler'));
            $this->processHandler('StartMigration',array('name'=>$name));
            try {
                $migration->down();
                $this->history->drop($name);
                $this->history->save();
            } catch ( \Exception $e ) {
                $this->processHandler('Error',array('message'=>$e->getMessage(), 'file'=>$e->getFile(), 'line'=>$e->getLine()));
            }
        }
        $this->processHandler('DoneMigration',array('count'=>$counter));
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
            $files[$index] = basename($file, '.php');
        }
        return array_values($files);
    }
    
    /**
     * @param unknown $process
     */
    public function processHandler( $name, $process ) {
        if ( !is_callable($this->processHandler) ) {
            return ;
        }
        call_user_func_array($this->processHandler, array($name, $process));
    }
}