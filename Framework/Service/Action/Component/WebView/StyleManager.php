<?php
namespace X\Service\Action\Component\WebView;
/**
 * 样式管理器
 * @author Michael Luthor <michaelluthor@163.com>
 */
class StyleManager {
    /**
     * 当前页面的样式列表
     * @var array
     */
    protected $styles = array(
    /* 'body@screen' => array(
     *   'item'  => 'body'
     *   'style' => array('background-color'=>'red'),
     *   'media' => 'screen',
     * ),
     */
    );
    
    /**
     * 增加样式到管理器
     * @param string $item 选择器
     * @param array $style 样式
     * @param string $media 设备类型 
     * @throws \Exception
     * @return void
     */
    public function add( $item, array $style, $media=null ) {
        if ( empty($item) || empty($style) ) {
            throw new \Exception('item or style can not be empty.');
        }
        
        $key = $this->getKeyForStyles($item, $media);
        if ( !isset($this->styles[$key]) ) {
            $this->styles[$key]['style'] = $style;
        } else {
            $this->styles[$key]['style'] = array_merge($this->styles[$key]['style'], $style);
        }
        $this->styles[$key]['item'] = $item;
        $this->styles[$key]['media'] = $media;
    }
    
    /**
     * 设置指定选择器的样式属性
     * @param string $item 选择器名称
     * @param string $attribute 属性值
     * @param mixed $value 属性值
     * @param string $media 设备类型
     * @return void
     */
    public function set($item,$attribute,$value,$media=null ) {
        if ( empty($item) || empty($attribute) || empty($value) ) {
            throw new \Exception('item, attribute or value can not be empty.');
        }
        
        $key = $this->getKeyForStyles($item, $media);
        if ( !isset($this->styles[$key]) ) {
            $this->styles[$key] = array();
            $this->styles[$key]['item'] = $item;
            $this->styles[$key]['media'] = $media;
        }
        $this->styles[$key]['style'][$attribute] = $value;
    }
    
    /**
     * 获取所有样式
     * @return array
     */
    public function getStyles( ) {
        return $this->styles;
    }
    
    /**
     * 获取指定选择器的样式属性
     * @param string $item 选择器名称
     * @param string $attribute 设备类型
     * @param string $media 设备类型
     * @return mixed
     */
    public function get($item, $attribute, $media=null) {
        $key = $this->getKeyForStyles($item, $media);
        if ( !isset($this->styles[$key]) ) {
            return null;
        } else if ( !isset($this->styles[$key]['style'][$attribute]) ) {
            return null;
        } else {
            return $this->styles[$key]['style'][$attribute];
        }
    }
    
    /**
     * 删除指定样式
     * @param string $item 选择器
     * @param string $media 设备类型
     * @return void
     */
    public function remove( $item, $media=null ) {
        $key = $this->getKeyForStyles($item, $media);
        if ( isset($this->styles[$key]) ) {
            unset($this->styles[$key]);
        }
    }
    
    /**
     * 移除指定样式的属性
     * @param string $item 选择器
     * @param string $name 属性名称
     * @param string $media 设备类型
     * @return void
     */
    public function removeAttribute( $item, $name, $media=null ) {
        $key = $this->getKeyForStyles($item, $media);
        if ( !isset($this->styles[$key]) ) {
            return;
        }
        
        if ( !isset($this->styles[$key]['style'][$name]) ) {
            return;
        }
        
        unset($this->styles[$key]['style'][$name]);
        if ( 0 === count($this->styles[$key]['style']) ) {
            unset($this->styles[$key]);
        }
    }
    
    /**
     * 将样式转为HTML代码
     * @return string
     */
    public function toString() {
        if ( 0 === count($this->styles) ) {
            return null;
        }
        
        $styleList = array();
        foreach ( $this->styles as $item => $attribute ) {
            $itemStyle = array();
            foreach ( $attribute['style'] as $name => $value ) {
                $itemStyle[] = $name.':'.$value;
            }
            $itemStyle = $attribute['item'].' {'.implode(';', $itemStyle).';}';
            if ( null !== $attribute['media'] ) {
                $itemStyle = '@media '.$attribute['media'].' { '.$itemStyle.' }';
            }
            $styleList[] = $itemStyle;
        }
        $styleList = implode("\n", $styleList);
        $styleList = '<style type="text/css">'."\n".$styleList."\n".'</style>';
        return $styleList;
    }
    
    /**
     * 生成样式表管理数组键名
     * @param string $item 选择器
     * @param string $media 设备名称
     * @return string
     */
    private function getKeyForStyles( $item, $media ) {
        return is_null($media) ? $item : sprintf('%s@%s', $item, $media);
    }
}