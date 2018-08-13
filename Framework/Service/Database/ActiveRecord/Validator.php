<?php
namespace X\Service\Database\ActiveRecord;
use X\Service\Database\ActiveRecord;
use X\Service\Database\DatabaseException;
use X\Service\Database\Query;
use X\Service\Database\Query\Expression;
use X\Service\Database\Query\Condition;
class Validator {
    /** @var Attribute */
    private $attribute = null;
    /** @var array */
    private $validators = array();
    
    /**
     * convert attribute defination part to validator, for example `NOT_NULL`
     * convert into `validateNotNull`, if validator does not exists, false will
     * be returned.
     * @param string $name
     * @return string|false
     */
    public static function formatAsBuildInValidator( $name ) {
        $name = explode('_', strtolower($name));
        $name = implode('', array_map('ucfirst', $name));
        $method = 'validate'.$name;
        return method_exists(self::class, $method) ? lcfirst($name) : false;
    }
    
    /**
     * @param Attribute $attribute
     */
    public function __construct( Attribute $attribute ) {
        $this->attribute = $attribute;
    }
    
    /**
     * @param unknown $validator
     * return self
     */
    public function addValidator( $validator ) {
        $this->validators[] = $validator;
        return $this;
    }
    
    /** @return boolean */
    public function validate() {
        $model = $this->attribute->getModel();
        
        foreach ( $this->validators as $validator ) {
            if ( is_string($validator) ) {
                $validatorName = 'validate'.ucfirst($validator);
                if ( is_callable(array($this, $validatorName)) ) {
                    $validator = array($this, $validatorName);
                } else if ( is_callable(array($model, $validatorName)) ) {
                    $validator = array($model, $validatorName);
                }
            }
            if ( !is_callable($validator) ) {
                throw new DatabaseException('validator is not available');
            }
            call_user_func_array($validator, array($model, $this->attribute));
        }
        return $model->getErrors($this->attribute->getName());
    }
    
    /** validate not null */
    public function validateNotNull( ActiveRecord $model, Attribute $attribute ) {
        if ( $attribute->getIsAutoIncrement() && $model->getIsNew() ) {
            return;
        }
        
        if ( null === $attribute->getValue() ) {
            $name = $attribute->getName();
            $model->addError($name, "{$name} can not be null");
        }
    }
    
    /** validate not empty */
    public function validateNotEmpty( ActiveRecord $model, Attribute $attribute ) {
        if ( $attribute->getIsAutoIncrement() && $model->getIsNew() ) {
            return;
        }
        
        $value = $attribute->getValue();
        if ( empty($value) ) {
            $name = $attribute->getName();
            $model->addError($name, "{$name} can not be empty");
        }
    }
    
    /** validate for unique */
    public function validateUnique( ActiveRecord $model, Attribute $attribute ) {
        if ( $attribute->getIsAutoIncrement() && $model->getIsNew() ) {
            return;
        }
        
        $attrName = $attribute->getName();
        $condition = Condition::build()->is($attrName, $attribute->getValue())
            ->add($model->getExceptCondition());
        
        $counter = Query::select($model->getDB())->expression(Expression::count(),'RowCount')
            ->from($model->tableName())
            ->where($condition)
            ->one();
        
        if ( "0" !== $counter['RowCount'] ) {
            $model->addError($attrName, "{$attrName} must be unique");
        }
    }
    
    public function validateUnsigned() {}
}