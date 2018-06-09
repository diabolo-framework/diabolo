<?php
namespace X\Core\Component;
class Directory {
    /**
     * List all files under the path
     * @param string $path
     * @return array
     */
    public static function listAllFiles( $path ) {
        if ( !is_dir($path) ) {
            return array();
        }
        
        $path = rtrim($path, DIRECTORY_SEPARATOR.'/').DIRECTORY_SEPARATOR;
        $files = array();
        foreach ( scandir($path) as $child ) {
            if ( '.' === $child || '..' === $child ) {
                continue;
            }
            $childPath = $path.$child;
            if ( is_dir($childPath) ) {
                $files = array_merge($files, self::listAllFiles($childPath));
            } else {
                $files[] = $childPath;
            }
        }
        
        return $files;
    }
}