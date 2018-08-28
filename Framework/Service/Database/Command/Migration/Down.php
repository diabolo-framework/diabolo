<?php
namespace X\Service\Database\Command\Migration;
use X\Core\X;
use X\Service\Database\Migrate;
use X\Service\Database\Migration\HistoryHandler\File;
class Down extends Up {
    /**
     * @param array $args
     * @param $0 step count, default to the last
     */
    public function run( $args=array() ) {
        $migrator = new Migrate(array(
            'namespace' => 'X\Migration',
            'scriptPath' => X::system()->getPath('Migration'),
            'processHandler' => array($this, 'processHandler'),
            'history' => array(
                'class' => File::class,
            ),
        ));
        
        $step = isset($args['params'][0]) ? $args['params'][0] : 1;
        $migrator->down($step);
        
        echo "\n\nMigration Done\n";
    }
}