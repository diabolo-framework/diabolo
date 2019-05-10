<?php
namespace X\Core\Component;
class Datetime {
    /**
     * 时间戳
     * @var integer
     **/
    private $timestamp = null;
    
    /**
     * @param unknown $datetime
     * @return self
     */
    public static function fromString( $datetime ) {
        $instance = new self();
        $instance->timestamp = strtotime($datetime);
        return $instance;
    }
    
    /**
     * 格式化时间戳到友好的时间差格式
     * @return string
     */
    public function getReadableDiffTimeFromNow() {
        if ( 0 > $this->timestamp ) { return '很久以前'; }
        
        $timestamp = time() - $this->timestamp;
        $diffName = 0 < $timestamp ? '前' : '后';
        $timestamp = abs($timestamp);
        
        $unitName = null;
        $units = array('秒'=>60,'分钟'=>60,'小时'=>24,'天'=>30, '个月'=>12);
        foreach ( $units as $unit => $value ) {
            if ( $timestamp < $value ) {
                $unitName = $unit;
                break;
            }
            $timestamp /= $value;
        }
        if ( null === $unitName ) {
            $unitName = '年';
            $timestamp /= intval($timestamp);
        }
        $timestamp = intval($timestamp);
        return "{$timestamp}{$unitName}{$diffName}";
    }
}