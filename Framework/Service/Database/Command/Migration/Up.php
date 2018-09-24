<?php
namespace X\Service\Database\Command\Migration;
use X\Core\X;
use X\Service\Database\Migrate;
use X\Service\Database\Migration\HistoryHandler\File;
class Up {
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
                'path' => (isset($args['options']['history-path'])) ? $args['options']['history-path'] : null,
            ),
        ));
        
        $step = isset($args['params'][0]) ? $args['params'][0] : 0;
        $migrator->up($step);
        
        echo "\n\nMigration Done\n";
    }
    
    /**
     * @param unknown $process
     */
    public function processHandler( $name, $process ) {
        switch ( $name ) {
        case 'StartMigration' : echo "processing : {$process['name']}\n"; break;
        case 'CreateTable'    : echo "    => create table : {$process['tableName']}\n"; break;
        case 'DropTable'      : echo "    => drop table : {$process['tableName']}\n"; break;
        case 'TruncateTable'  : echo "    => truncate table : {$process['tableName']}\n"; break;
        case 'RenameTable'    : echo "    => rename table : {$process['tableName']} -> {$process['newName']}\n"; break;
        case 'AddColumn'      : echo "    => add column : {$process['tableName']}.{$process['colName']}\n"; break;
        case 'DropColumn'     : echo "    => drop column : {$process['tableName']}.{$process['colName']}\n"; break;
        case 'RenameColumn'   : echo "    => rename column : {$process['tableName']}.{$process['colName']} -> {$process['newName']}\n"; break;
        case 'ChangeColumn'   : echo "    => change column : {$process['tableName']}.{$process['colName']}\n"; break;
        case 'AddIndex'       : echo "    => add index : {$process['tableName']}.{$process['indexName']}\n"; break;
        case 'DropIndex'      : echo "    => drop index : {$process['tableName']}.{$process['indexName']}\n"; break;
        case 'AddForginKey'   : echo "    => add forgin key : {$process['tableName']}.{$process['fkName']}\n"; break;
        case 'DropForginKey'  : echo "    => drop forgin key : {$process['tableName']}.{$process['fkName']}\n"; break;
        case 'DeleteData'     : echo "    => delete data : {$process['tableName']} [{$process['count']} row(s)] - {$process['message']}\n"; break;
        case 'UpdateData'     : echo "    => update data : {$process['tableName']} [{$process['count']} row(s)] - {$process['message']}\n"; break;
        case 'Message'        : echo "    => [msg] : {$process['text']} \n"; break;
        case 'DoneMigration'  : echo "\n\ndone migration : {$process['count']} executed.\n"; break;
        case 'Error'          : echo "\n\nmigration failed : {$process['message']}\nLocation:{$process['file']}#{$process['line']}\n"; exit();
        default : echo "unknown process action `{$name}`\n"; break;
        }
    }
}