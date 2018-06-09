<?php
namespace X\Core\Component;
/**
 * 操作 / 获取类信息的帮助类
 * @author Michael Luthor <michaelluthor@163.com>
 */
class ClassHelper {
    /**
     * 根据类和相对路径获取绝对路径。
     * @param string|object $class 类名或对象实例
     * @param string $path 相对路径
     * @return string
     */
    public static function getPathRelatedClass( $class, $path ){
        $classInfo = new \ReflectionClass(is_string($class) ? $class : get_class($class));
        $classPath = dirname($classInfo->getFileName());
        $path = (null===$path) ? $classPath : $classPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
        return $path;
    }
    
    /**
     * parse document comment
     * @param string $comment
     * @return array
     */
    public static function parseDocComment( $comment ) {
        $comment = trim($comment, "/*");
        $comment = str_replace("\r", "", $comment);
        $comment = explode("\n", $comment);
        
        # parse into items
        $commentItems = array(array('key'=>'description','value'=>''));
        foreach ( $comment as $line ) {
            $line = ltrim(trim($line), '* ');
            if ( empty($line) ) {
                continue;
            }
            if ( '@' === $line[0] ) {
                $keySepPos = strpos($line, ' ');
                $key = substr($line, 1, $keySepPos);
                $line = substr($line, $keySepPos+1);
            } else {
                $item = array_pop($commentItems);
                $key = $item['key'];
                $line = $item['value']."\n".$line;
            }
            $commentItems[] = array('key'=>$key, 'value'=>$line);
        }
        
        # setup comment information into array
        $commentInfo = array();
        foreach ( $commentItems as $item ) {
            $item['key'] = trim($item['key']);
            $item['value'] = trim($item['value']);
            switch ( $item['key'] ) {
            case 'param' : 
                preg_match('#^(?P<type>[\w|\\\\]+)\s+\$(?P<name>\w+)\s*(?P<desc>.*)$#is', $item['value'], $paramMatch);
                $item['value'] = array(
                    'type' => $paramMatch['type'],
                    'name' => $paramMatch['name'],
                    'description' => $paramMatch['desc'],
                );
                break;
            }
            
            if ( !isset($commentInfo[$item['key']]) ) {
                $commentInfo[$item['key']] = array();
            }
            $commentInfo[$item['key']][] = $item['value'];
        }
        return $commentInfo;
    }
}