<?php
namespace X\Service\Database\Command\Migration;
use X\Core\X;
class Create {
    /**
     * create new migration file.
     * @param $0 name of migration file
     */
    public function run( $args=array() ) {
        if ( !isset($args['params'][0]) ){
            throw new \Exception('migration\'s name is not set.' );
        }
        
        $migrationName = $args['params'][0];
        $filePath = $this->generateScriptFilePath($migrationName, $className);
        $fileContent = $this->generateScirptFileContent($className);
        file_put_contents($filePath, $fileContent);
        
        echo "migration file created at : \n{$filePath}\n";
    }
    
    /**
     * @param unknown $className
     * @return string
     */
    private function generateScirptFileContent( $className ) {
        $content = <<<EOF
<?php
namespace X\\Migration;
use X\\Service\\Database\\Migration\\Migration;
class {$className} extends Migration {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\Migration::getDb()
     */
    protected function getDb() {
        # @TODO : setup the database config name here
        return 'default';
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\Migration::up()
     */
    public function up() {
        # @TODO : update database here
    }

    /**
     * {@inheritDoc}
     * @see \X\Service\Database\Migration\Migration::down()
     */
    public function down() {
        # @TODO : rollback database here
    }
}
EOF;
        return $content;
    }
    
    /**
     * @param string $name
     * @return string
     */
    private function generateScriptFilePath( $name, &$className ) {
        $migrationFolder = X::system()->getPath('Migration/');
        if ( !is_dir($migrationFolder) ) {
            mkdir($migrationFolder);
        }
        
        $className = sprintf('M%05d_%s',count(glob($migrationFolder.'M*.php')), $name);
        return $migrationFolder.$className.'.php';
    }
}