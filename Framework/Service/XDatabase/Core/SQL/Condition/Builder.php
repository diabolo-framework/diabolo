<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\SQL\Condition;

/**
 * SQLConditionBuilder
 * @author  Michael Luthor <michael.the.ranidae@gmail.com>
 * @since   0.0.0
 * @version 0.0.0
 */
class Builder {
    /**
     * This value contains all the conditions
     * @var Condition[]
     */
    protected $conditions = array();
    
    /**
     * Create a new condition Builder.
     * @param mixed $condition The initiate condtion for condition Builder
     * @return Builder
     */
    public static function build( $condition=null ) {
        $builderName = get_called_class();
        $builder = new $builderName();
        if ( null !== $condition ) {
            $builder->addCondition($condition);
        }
        return $builder;
    }
    
    /**
     * Add condition part to condition Builder. 
     * @param string|array $condition -- The condition to add.
     * @return Builder
     */
    public function addCondition( $condition ) {
        if ( is_array($condition) || ($condition instanceof \Iterator) ) {
            foreach ( $condition as $name => $value ) {
                if ( is_array($value) ||  ($value instanceof \Iterator) ) {
                    $con = new Condition($name, Condition::OPERATOR_IN, $value);
                } else {
                    $con = new Condition($name, Condition::OPERATOR_EQUAL, $value);
                }
                $this->addCondition($con);
            }
        } else if ( is_string($condition) || $condition instanceof Condition || $condition instanceof Builder ) {
            $this->conditions[] = $condition;
            $this->addConnector(Connector::CONNECTOR_AND);
        } else if ( method_exists($condition, 'toString') ) {
            $this->addCondition($condition->toString());
        }
        return $this;
    }
    
    /**
     * Add a single condition into current condition query.
     * @param string $name The name of the column
     * @param string $operation The operator index of condition
     * @param mixed $value The value part of the condition.
     * @return Builder
     */
    public function addSigleCondition( $name, $operation, $value ) {
        $con = new Condition($name, $operation, $value);
        $this->addCondition($con);
        return $this;
    }
    
    /**
     * Add connector for condition.
     * @param integer $connector Connector::CONNECTOR_*
     * @return Builder
     */
    public function addConnector( $connector = Connector::CONNECTOR_AND ) {
        $connector = new Connector($connector);
        if (!empty($this->conditions) && $this->conditions[count($this->conditions)-1] instanceof Connector ) {
            $this->conditions[count($this->conditions)-1] = $connector;
        } else {
            $this->conditions[] = $connector;
        }
        return $this;
    }
    
    /**
     * Add "is" condition into current condition group. 
     * @param string $name The name of column
     * @param mixed $value The value that column is.
     * @return Builder
     */
    public function is( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_EQUAL, $value);
        return $this;
    }
    
    /**
     * Add "is not" condition into current condition group. 
     * @param string $name The name of column.
     * @param mixed $value The value that column is not.
     * @return Builder
     */
    public function isNot( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_NOT_EQUAL, $value);
        return $this;
    }
    
    /**
     * Add "equals" condition into current condition group. 
     * @param string $name The name of the column
     * @param mixed $value The value that column is
     * @return Builder
     */
    public function equals( $name, $value ) {
        return $this->is($name, $value);
    }
    
    /**
     * Add "not equals" condition into current condition group. 
     * @param string $name The name of column.
     * @param mixed $value The value that column not equals.
     * @return Builder
     */
    public function notEquals( $name, $value ) {
        return $this->isNot($name, $value);
    }
    
    /**
     * Add "greater than" condition into current condition group. 
     * @param string $name The name of the column.
     * @param mixed $value The value that column greater than.
     * @return Builder
     */
    public function greaterThan( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_GREATER_THAN, $value);
        return $this;
    }
    
    /**
     * Add "greater or equals" condition into current condition group. 
     * @param string $name The name of the column
     * @param mixed $value The value that column greateer or equals
     * @return Builder
     */
    public function greaterOrEquals( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_GREATER_OR_EQUAL, $value);
        return $this;
    }
    
    /**
     * Add "less than" condition into current condition group. 
     * @param string $name The name of the column
     * @param mixed $value The value of the column less than
     * @return Builder
     */
    public function lessThan( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_LESS_THAN, $value);
        return $this;
    }
    
    /**
     * Add "less or equals" condition into current condition group. 
     * @param string $name The name of the column 
     * @param mixed $value The value of the column less or equals.
     * @return Builder
     */
    public function lessOrEquals( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_LESS_OR_EQUAL, $value);
        return $this;
    }
    
    /**
     * Add "like" condition into current condition group. 
     * @param string $name The name of the column
     * @param string $value The value that column likes
     * @return Builder
     */
    public function like( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_LIKE, $value);
        return $this;
    }
    
    /**
     * Add "start with" condition into current condition group. 
     * @param string $name The name of the column
     * @param string $value The value that column start with
     * @return Builder
     */
    public function startWith( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_START_WITH, $value);
        return $this;
    }
    
    /**
     * Add "end with" condition into current condition group. 
     * @param string $name The name of column
     * @param string $value The value of column end with
     * @return Builder
     */
    public function endWith( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_END_WITH, $value);
        return $this;
    }
    
    /**
     *  Add "includes" condition into current condition group. 
     * @param string $name
     * @param array $value
     * @return \X\Database\SQL\Condition\Builder
     */
    public function includes( $name, $value ) {
        $this->addSigleCondition($name, Condition::OPERATOR_INCLUDES, $value);
        return $this;
    }
    
    /**
     * Add "between" condition into current condition group. 
     * @param string $name The name of column
     * @param mixed $minValue The min value that column greater than
     * @param mixed $maxValue The max value that column less than
     * @return Builder
     */
    public function between( $name, $minValue, $maxValue ) {
        $this->addSigleCondition($name, Condition::OPERATOR_BETWEEN, array($minValue, $maxValue));
        return $this;
    }
    
    /**
     * Add "in" condition into current condition group. 
     * @param string $name The name of column
     * @param array $values The values that column could be.
     * @return Builder
     */
    public function in( $name, $values ) {
        $this->addSigleCondition($name, Condition::OPERATOR_IN, $values);
        return $this;
    }
    
    /**
     * Add "not in" condition into current condition group. 
     * @param string $name The name of the column
     * @param array $values The value that column could not be.
     * @return Builder
     */
    public function notIn( $name, $values ) {
        $this->addSigleCondition($name, Condition::OPERATOR_NOT_IN, $values);
        return $this;
    }
    
    /**
     * Add "exists" condition into current condition group. 
     * @param string $condition
     * @return \X\Service\XDatabase\Core\SQL\Condition\Builder
     */
    public function exists( $condition ) {
        $this->addSigleCondition(null, Condition::OPERATOR_EXISTS, $condition);
        return $this;
    }
    
    /**
     * Add "not exists" condition into current condition group.
     * @param string $condition
     * @return \X\Service\XDatabase\Core\SQL\Condition\Builder
     */
    public function notExists( $condition ) {
        $this->addSigleCondition(null, Condition::OPERATOR_NOT_EXISTS, $condition);
        return $this;
    }
    
    /**
     * Add "and" connector into current condition group. 
     * @return Builder
     */
    public function andAlso() {
        if ( !empty($this->conditions) ) {
            $this->addConnector(Connector::CONNECTOR_AND);
        }
        
        return $this;
    }
    
    /**
     * Add "or" connector into current condition group. 
     * @return Builder
     */
    public function orThat() {
        if ( !empty($this->conditions) ) {
            $this->addConnector(Connector::CONNECTOR_OR);
        }
        return $this;
    }
    
    /**
     * Add group start into current condition group. 
     * @return Builder
     */
    public function groupStart() {
        $this->conditions[] = Group::start();
        return $this;
    }
    
    /**
     * Add group end mark into current condition group. 
     * @return Builder
     */
    public function groupEnd() {
        if ($this->conditions[count($this->conditions)-1] instanceof Connector ) {
            array_splice($this->conditions, count($this->conditions)-1, 1);
        }
        
        $this->conditions[] = Group::end();
        $this->addConnector(Connector::CONNECTOR_AND);
        return $this;
    }
    
    /**
     * Convert current condition group into string.
     * @return string
     */
    public function toString() {
        $conditions = array();
        foreach ( $this->conditions as $condition ) {
            $conditions[] = is_string($condition) ? $condition : $condition->toString();
        }
        
        if ( empty($conditions) ) {
            return '';
        }
        
        /* 去掉空白的条件。 */
        for ( $i=0; $i<count($conditions); $i++ ) {
            if ( !empty($conditions[$i]) ) {
                continue;
            }
            unset($conditions[$i]);
            unset($conditions[$i+1]);
            $i++;
        }
        array_pop($conditions);
        $condition = implode(' ', $conditions);
        return $condition;
    }
}