<?php
namespace X\Core\Util;

/**
 * @author Michael Luthor <michaelluthor@163.com> 
 * @version 0.0.0
 */
class ConfigurationArray implements \ArrayAccess, \Iterator {
    /**
     * This value contains the real array values.
     * @var array
     */
    private $data = array();
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
    */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->data);
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function &offsetGet($offset) {
        if ( $this->offsetExists($offset) ) {
            return $this->data[$offset];
        } else {
            $value = null;
            return $value;
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        if (empty($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::current()
    */
    public function current() {
        return current($this->data);
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::next()
    */
    public function next() {
        return next($this->data);
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::key()
    */
    public function key() {
        return key($this->data);
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::valid()
    */
    public function valid() {
        return $this->offsetExists($this->key());
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::rewind()
    */
    public function rewind() {
        return reset($this->data);
    }
    
    /**
     * The destructor method will be called as soon as
     * there are no other references to a particular object,
     * or in any order during the shutdown sequence.
     * here, we set array data to null.
     * @link http://php.net/manual/en/language.oop5.decon.php#object.destruct
     */
    public function __destruct() {
        $this->data = null;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return \X\Core\Util\ConfigurationArray
     */
    public function set( $name, $value ) {
        $this->data[$name] = $value;
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get( $name, $default=null ) {
        if ( $this->offsetExists($name) ) {
            return $this->data[$name];
        } else {
            return $default;
        }
    }
    
    /**
     * @param unknown $values
     */
    public function setValues( $values ) {
        $this->merge($values);
    }
    
    /**
     * @param array $value
     * @param boolean $recursive
     */
    public function merge( $value ) {
        if ( !is_array($value) ) {
            throw new Exception('Unable to merge a non array value to configuration array.');
        }
        
        $this->doMerge($value, $this->data);
    }
    
    /**
     * @param unknown $source
     * @param unknown $target
     */
    private function doMerge( $source, &$target ) {
        foreach ( $source as $key => $value ) {
            if ( !array_key_exists($key, $target) ) {
                $target[$key] = $value;
            } else if ( is_array($value) ) {
                $this->doMerge($value, $target[$key]);
            } else {
                $target[$key] = $value;
            }
        }
    }
    
    /**
     * @param string|integer $name
     * @return boolean
     */
    public function has( $name ) {
        return $this->offsetExists($name);
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function remove( $name ) {
        if ( $this->offsetExists($name) ) {
            unset($this->data[$name]);
        }
    }
    
    /**
     * @return void
     */
    public function removeAll() {
        $this->data = array();
    }
    
    /**
     * @return number
     */
    public function getLength() {
        return count($this->data);
    }
    
    /**
     * @return array
     */
    public function toArray() {
        return $this->data;
    }
}