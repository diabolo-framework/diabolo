<?php
/**
 * 
 */
namespace X\Core\Util;

/**
 * 
 */
use X\Core\Util\Exception;

/**
 * 
 */
class XUtil {
    /**
     * 将php数组保存到指定文件中。
     * 
     * @param string $path 目标文件路径。
     * @param array $data 数组内容。
     */
    public static function storeArrayToPHPFile( $path, $data ) {
        $var = var_export($data, true);
        $content = array();
        $content[] = '<?php';
        $content[] = "return $var;";
        $content = implode("\n", $content);
        
        if ( (file_exists($path)&&is_writable($path))
        ||   (!file_exists($path)&&is_writable(dirname($path)))) {
            file_put_contents($path, $content);
        } else {
            throw new Exception('Unable to save file to "'.$path.'", it\'s unwritable.');
        }
    }
    
    /**
     * 根据路径删除指定的文件， 文件可以是普通文件或者目录。 如果指定路径不存在，则直接返回。
     * @param string $path 要删除文件的路径。
     */
    public static function deleteFile( $path ) {
        if ( !is_file($path) && !is_dir($path) ) {
            throw new Exception('('.$path.'): no such file or directory.');
        }
        
        if ( !is_dir($path) ) {
            unlink($path);
        } else {
            $files = scandir($path);
            foreach ( $files as $file ) {
                if ( '.' === $file || '..' === $file ) { continue; }
                self::deleteFile($path.DIRECTORY_SEPARATOR.$file);
            }
            rmdir($path);
        }
    }
    
    /**
     * @param unknown $from
     * @param unknown $to
     */
    public static function copyPath( $from, $to ) {
        if ( is_dir($from) ) {
            if (!is_dir($to)) {
                mkdir($to, 0777, true);
            }
            
            if ( DIRECTORY_SEPARATOR !== $from[strlen($from)-1] ) {
                $from .= DIRECTORY_SEPARATOR;
            }
            
            if ( DIRECTORY_SEPARATOR !== $to[strlen($to)-1] ) {
                $to .= DIRECTORY_SEPARATOR;
            }
            
            $files = scandir($from);
            foreach ( $files as $file ) {
                if ( '.' === $file || '..' === $file ) { continue; }
                $fromPath = $from.$file;
                $toPath = $to.$file;
                self::copyPath($fromPath, $toPath);
            }
        } else {
            copy($from, $to);
        }
    }
    
    /**
     * @param unknown $class
     * @param unknown $path
     * @return string
     */
    public static function getPathRelatedClass( $class, $path ){
        $classInfo = new \ReflectionClass(is_string($class) ? $class : get_class($class));
        $classPath = dirname($classInfo->getFileName());
        $path = (null===$path) ? $classPath : $classPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
        return $path;
    }
}