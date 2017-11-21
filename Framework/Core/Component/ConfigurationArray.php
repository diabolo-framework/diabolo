<?php
namespace X\Core\Component;
/**
 * 配置数组 用于从数组中读取配置信息
 * @author Michael Luthor <michaelluthor@163.com> 
 */
class ConfigurationArray implements \ArrayAccess, \Iterator {
    /**
     * 配置信息
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
     * 设置参数
     * @param string $name 参数名称
     * @param mixed $value  参数值
     * @return ConfigurationArray
     */
    public function set( $name, $value ) {
        $this->data[$name] = $value;
        return $this;
    }
    
    /**
     * 获取参数
     * @param string $name 参数名称
     * @param mixed $default 默认值
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
     * 批量设置参数
     * @param array $values 参数列表
     * @return \X\Core\Component\ConfigurationArray
     */
    public function setValues( array $values ) {
        $this->merge($values);
        return $this;
    }
    
    /**
     * 批量设置参数
     * @param array $value
     * @param boolean $recursive
     * @return \X\Core\Component\ConfigurationArray
     */
    public function merge( array $value ) {
        if ( !is_array($value) ) {
            throw new Exception('Unable to merge a non array value to configuration array.');
        }
        
        $this->doMerge($value, $this->data);
        return $this;
    }
    
    /**
     * 递归的合并数组
     * @param array $source 源数组
     * @param array $target 目标数组
     * @return void
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
     * 检查配置中是否存在指定的配置项目
     * @param string|integer $name 配置名称
     * @return boolean
     */
    public function has( $name ) {
        return $this->offsetExists($name);
    }
    
    /**
     * 从配置中移除指定的项目
     * @param string $name 配置名称
     * @return void
     */
    public function remove( $name ) {
        if ( $this->offsetExists($name) ) {
            unset($this->data[$name]);
        }
    }
    
    /**
     * 清空当前配置
     * @return void
     */
    public function removeAll() {
        $this->data = array();
    }
    
    /**
     * 获取配置项目数量
     * @return number
     */
    public function getLength() {
        return count($this->data);
    }
    
    /**
     * 将配置信息转换为数组
     * @return array
     */
    public function toArray() {
        return $this->data;
    }
}