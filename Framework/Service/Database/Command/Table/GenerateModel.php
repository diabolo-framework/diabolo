<?php
namespace X\Service\Database\Command\Table;
use X\Core\X;
use X\Service\Database\Service;
use X\Service\Database\Table;
use X\Service\Database\ActiveRecord;

class GenerateModel {
    /** Default path to storage model files */
    public $path = 'Model';
    /** Default namespace to model class */
    public $namespace = 'X\\Model';
    /** Default active record parent model name */
    public $parent = ActiveRecord::class;
    
    /**
     * create new migration file.
     * @param $0 string database_name.table_name 
     */
    public function run( $args=array() ) {
        if ( !isset($args['params'][0]) ) {
            throw new \Exception("database and table name should be specified");
        }
        if ( false === strpos($args['params'][0], '.') ) {
            throw new \Exception('specify database and table name by dot, example : database.table');
        }
        list($dbname, $tableName) = explode('.', $args['params'][0]);
        
        $modelData = array();
        $modelData['namespace'] = $this->namespace;
        $modelData['parentClassName'] = $this->parent;
        $modelData['className'] = $this->formatTableNameToModelClassName($tableName);
        $modelData['parentClassShortName'] = $this->getModelParentClassShortName();
        $modelData['dbname'] = $dbname;
        $modelData['tableName'] = $tableName;
        $modelData['columns'] = $this->getColumnDefinations($dbname, $tableName);
        $modelContent = $this->renderModelFileContent($modelData);
        
        $modelPath = X::system()->getPath($this->path)."/{$modelData['className']}.php";
        file_put_contents($modelPath, $modelContent);
        
        echo "Model File Generated At :\n";
        echo $modelPath, "\n";
    }
    
    /**
     * 获取表列描述信息
     * @return array
     */
    private function getColumnDefinations( $dbname, $tableName) {
        $table = Table::get($dbname, $tableName);
        if ( null === $table ) {
            throw new \Exception("table `{$tableName}` does not exists.");
        }
        $columns = $table->getColumns();
        foreach ( $columns as $index => $column ) {
            $defination = array();
            
            $dataType = strtolower($column->getType());
            switch ( $dataType ) {
            case 'varchar'  : $defination[] = "string({$column->getLength()})"; break;
            case 'longtext' : $defination[] = "text"; break;
            default :
                $defination[] = $dataType;
                break;
            }
            
            switch ( $dataType ) {
            case 'longtext':
            case 'datetime':
            case 'varchar': $dataType = 'string'; break;
            }
            
            
            if ( $column->getIsPrimary() ) {
                $defination[] = 'PRIMARY_KEY';
            }
            if ( $column->getIsAutoIncrement() ) {
                $defination[] = 'AUTO_INCREASE';
            }
            if ( $column->getIsNotNull() ) {
                $defination[] = 'not-null';
            }
            
            $columns[$index] = [
                'name' => $index,
                'type' => $dataType,
                'defination'=>implode(' ', $defination),
            ];
        }
        return $columns;
    }
    
    /**
     * 获取扩展类的短名称
     * @return string
     */
    private function getModelParentClassShortName() {
        $parent = explode('\\', $this->parent);
        return array_pop($parent);
    }
    
    /**
     * 格式化模型名称
     * @param unknown $tableName
     * @return string
     */
    private function formatTableNameToModelClassName( $tableName ) {
        # 下划线改为驼峰
        $tableName = explode('_', $tableName);
        $tableName = array_map('ucfirst', $tableName);
        $tableName = implode('', $tableName);
        return $tableName;
    }
    
    /**
     * 渲染model文件内容
     * @param array $modelData
     */
    private function renderModelFileContent( array $modelData ) {
        ob_start();
        ob_implicit_flush(false);
        ?>
namespace <?php echo $modelData['namespace']; ?>;
use <?php echo $modelData['parentClassName']; ?>;
/**
<?php foreach ( $modelData['columns'] as $column ) : ?>
 * @property <?php echo $column['type']?> $<?php echo $column['name']?> 
<?php endforeach;?>
 * @since <?php echo date('Y-m-d H:i:s');?> 
 */
class <?php echo $modelData['className']; ?> extends <?php echo $modelData['parentClassShortName']; ?> {
    /**
     * get database config name
     * @return string
     */
    public static function getDB() {
        return '<?php echo $modelData['dbname']; ?>';
    }
    
    /**
     * get table name
     * @return string
     */
    public static function tableName() {
        return '<?php echo $modelData['tableName']; ?>';
    }
    
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\ActiveRecord::getDefination()
     */
    protected function getDefination() {
        return array(
        <?php foreach ( $modelData['columns'] as $column ) : ?>
    '<?php echo $column['name']?>' => '<?php echo $column['defination'];?>',
        <?php endforeach;?>
);
    }
}
<?php 
        $content = ob_get_clean();
        
        $content = "<?php\n{$content}";
        return $content;
    }
}