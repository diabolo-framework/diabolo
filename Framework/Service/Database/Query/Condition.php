<?php
namespace X\Service\Database\Query;
use X\Service\Database\Database;
use X\Service\Database\DatabaseException;
use X\Service\Database\Driver\DatabaseDriver;
/**
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Condition {
    /** condition type for condition  */
    const TYPE_CONDITION = 1;
    /** condition type for connector  */
    const TYPE_CONNECTOR = 2;
    /** condition type for custom condition  */
    const TYPE_CUSTOM_CONDITION = 3;
    
    /** @var Database */
    private $database = null;
    /** @var array */
    private $params = array();
    /** @var string */
    private $cache = null;
    /** @var mixed */
    private $conditions = array(
        # array( 'type' => 'condition', content => '1=1' )
        # array( 'type' => 'connector', content=>and )
    );
    
    /** @return self */
    public static function build( ) {
        return new static();
    }
    
    /** @return self */
    public function setDatabase( Database $db ) {
        $this->cache = null;
        $this->database = $db;
        return $this;
    }
    
    /** @return boolean */
    public function isEmpty() {
        return empty($this->conditions);
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function is( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => '=',
                'value' => $value,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function isNot( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => '<>',
                'value' => $value,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function equals( $name, $value ) {
        $this->cache = null;
        return $this->is($name, $value);
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function notEquals( $name, $value ) {
        $this->cache = null;
        return $this->isNot($name, $value);
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function lessThan( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => '<',
                'value' => $value,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function lessOrEqual( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => '<=',
                'value' => $value,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function greaterThan( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => '>',
                'value' => $value,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function greaterOrEqual( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => '>=',
                'value' => $value,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function contains( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'LIKE',
                'value' => '%'.$value.'%',
            ),
        );
        return $this;
    }
    
    /**
     * @param unknown $name
     * @param unknown $value
     * @return self
     */
    public function like( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'LIKE',
                'value' => $value,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function notContains( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'NOT LIKE',
                'value' => '%'.$value.'%',
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function beginWith( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'LIKE',
                'value' => $value.'%',
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function endWith( $name, $value ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'LIKE',
                'value' => '%'.$value,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @return self
     */
    public function isNull( $name ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'IS NULL',
                'value' => null,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @return self
     */
    public function isNotNull( $name ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'IS NOT NULL',
                'value' => null,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $min
     * @param mixed $max
     * @return self
     */
    public function between( $name, $min, $max ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'BETWEEN',
                'value' => array('min'=>$min, 'max'=>$max),
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $min
     * @param mixed $max
     * @return self
     */
    public function notBetween( $name, $min, $max ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'NOT BETWEEN',
                'value' => array('min'=>$min, 'max'=>$max),
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $list
     * @return self
     */
    public function in( $name, $list ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'IN',
                'value' => $list,
            ),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $list
     * @return self
     */
    public function notIn( $name, $list ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONDITION,
            'content' => array(
                'expr' => $name,
                'operator' => 'NOT IN',
                'value' => $list,
            ),
        );
        return $this;
    }
    
    /**
     * @param string|array|self $condition
     * @return self
     */
    public function add($condition) {
        if ( null === $condition ) {
            return $this;
        }
        $this->cache = null;
        $this->conditions[] = array(
            'type'=>self::TYPE_CUSTOM_CONDITION,
            'content'=> $condition,
        );
        return $this;
    }
    
    /** @return self */
    public function andThat( ) {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONNECTOR,
            'content' => 'AND',
        );
        return $this;
    }
    
    /** @return self */
    public function orThat() {
        $this->cache = null;
        $this->conditions[] = array(
            'type' => self::TYPE_CONNECTOR,
            'content' => 'OR',
        );
        return $this;
    }
    
    /**
     * @param array $params
     * @return \X\Service\Database\Query\Condition
     */
    public function setPreviousParams( &$params ) {
        $this->params = &$params;
        return $this;
    }
    
    /** @return array */
    public function getBindParams() {
        if ( null === $this->cache ) {
            $this->toString();
        }
        return $this->params;
    }
    
    /** @return string  */
    public function toString() {
        if ( null !== $this->cache ) {
            return $this->cache;
        }
        
        $conditions = array();
        $lastConditionElemType = null;
        foreach ( $this->conditions as $condition ) {
            $conditionStr = null;
            
            if ( self::TYPE_CONNECTOR === $condition['type'] ) {
                $conditionStr = $condition['content'];
            } else if ( self::TYPE_CONDITION === $condition['type'] ){
                $conditionStr = $this->convertNormalConditionToString($condition['content']);
            } else if ( self::TYPE_CUSTOM_CONDITION === $condition['type'] ) {
                $conditionStr = $this->convertCustomConditionToString($condition['content']);
            }
            
            if ( null !== $lastConditionElemType 
            && self::TYPE_CONNECTOR !== $lastConditionElemType 
            && self::TYPE_CONNECTOR !== $condition['type'] ) {
                $conditions[] = 'AND';
            }
            $conditions[] = $conditionStr;
            $lastConditionElemType = $condition['type'];
        }
        
        if ( self::TYPE_CONNECTOR === $lastConditionElemType ) {
            array_pop($conditions);
        }
        
        $this->cache = '( '.implode(' ', $conditions).' )';
        return $this->cache;
    }
    
    /**
     * @param mixed $condition
     * @return string
     */
    private function convertCustomConditionToString( $condition ) {
        if ( $condition instanceof Condition ) {
            $condition->setPreviousParams($this->params);
            $condition->setDatabase($this->database);
            return $condition->toString();
        } else if ( is_string($condition) ) {
            return $condition;
        } else if ( is_array($condition) ) {
            $conditionParts = array();
            foreach ( $condition as $key => $value ) {
                $operator = (is_array($value) || ($value instanceof DatabaseQuery)) ? 'IN' : '=';
                $content = array(
                    'expr' => $key,
                    'operator' => $operator,
                    'value' => $value
                );
                $conditionParts[] = $this->convertNormalConditionToString($content);
            }
            return implode(' AND ', $conditionParts);
        } else {
            throw new DatabaseException('custom condition format error');
        }
    }
    
    /**
     * @param array $condition
     * @return string
     */
    private function convertNormalConditionToString( array $condition ) {
        $expr = $this->database->quoteExpression($condition['expr']);
        
        switch ( $condition['operator'] ) {
        case 'IS NULL': 
        case 'IS NOT NULL': 
            return "{$expr} {$condition['operator']}";
        case 'NOT BETWEEN' :
        case 'BETWEEN' :
            $paramsKeyMin = $this->getParamKey($condition['value']['min'],$condition['expr']);
            $paramsKeyMax = $this->getParamKey($condition['value']['max'],$condition['expr']);
            return "{$expr} {$condition['operator']} {$paramsKeyMin} AND {$paramsKeyMax}";
        case 'IN' :
        case 'NOT IN' :
            if ( $condition['value'] instanceof DatabaseQuery ) {
                $condition['value']->setPreviousParams($this->params);
                $condition['value']->setDatabase($this->database);
                return "{$expr} {$condition['operator']} ({$condition['value']->toString()})";
            }
            
            $markList = array();
            foreach ( $condition['value'] as $value ) {
                $markList[] = $this->getParamKey($value,$condition['expr']);
            }
            $markList = implode(', ', $markList);
            return "{$expr} {$condition['operator']} ( $markList )";
        default :
            $paramsKey = $this->getParamKey($condition['value'],$condition['expr']);
            return "{$expr} {$condition['operator']} {$paramsKey}";
        }
    }
    
    /**
     * @param mixed $value
     * @return string
     */
    private function getParamKey( $value, $expr ) {
        $prepareCustomExpr = $this->database->getDriver()->getOption(DatabaseDriver::OPT_PREPARE_CUSTOM_EXPRESSION, true);
        if ( !is_string($expr) && !$prepareCustomExpr ) {
            return is_string($value) ? $this->database->quoteValue($value) : $value;
        }
        
        if ( $value instanceof Expression ) {
            return $value->toString();
        } else if ( $value instanceof DatabaseQuery ) {
            return $value->toString();
        }
        
        $paramsKey = ':qp'.count($this->params);
        $this->params[$paramsKey] = $value;
        return $paramsKey;
    }
    
    /** @return string  */
    public function __toString() {
        return $this->toString();
    }
}