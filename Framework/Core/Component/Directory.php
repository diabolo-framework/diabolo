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
    
    /**
     * Delete a directory
     * @param string $path
     * @throws \Exception
     */
    public static function delete( $path ) {
        if ( !is_dir($path) ) {
            throw new \Exception("`{$path}` is not a directory");
        }
        
        $path = rtrim($path, DIRECTORY_SEPARATOR.'/').DIRECTORY_SEPARATOR;
        foreach ( scandir($path) as $child ) {
            if ( '.' === $child || '..' === $child ) {
                continue;
            }
            
            $childPath = $path.$child;
            if ( is_dir($childPath) ) {
                self::delete($childPath);
                if ( !@rmdir($childPath) ) {
                    throw new \Exception("failed to delete directory `{$childPath}`");
                }
            } else {
                if ( !@unlink($childPath) ) {
                    throw new \Exception("failed to delete file `{$childPath}`");
                }
            }
        }
        if ( !rmdir($path) ) {
            throw new \Exception("failed to delete directory `{$childPath}`");
        }
    }
}