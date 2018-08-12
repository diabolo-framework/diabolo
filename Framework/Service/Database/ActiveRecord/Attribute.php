<?php
namespace X\Service\Database\ActiveRecord;
use X\Service\Database\DatabaseException;
use X\Service\Database\ActiveRecord;

class Attribute {
    /** attribute types for varchar,nvarchar */
    const TYPE_STRING = 'STRING';
    /** attribute type for int */
    const TYPE_INT = 'INT';
    
    private $name = null;
    /** @var string type of attribute */
    private $type = self::TYPE_STRING;
    /** @var int length of attribute */
    private $length = 255;
    /** @var boolean primary key mark */
    private $isPrimaryKey = false;
    /** @var boolean auto increament mark */
    private $isAutoIncrement = false;
    /** @var mixed default value for attribute */
    private $defaultVal = null;
    
    /** @var Validator */
    private $validator = null;
    /** @var mixed current value of attribute */
    private $value = null;
    /** @var mixed old value of attribute */
    private $oldValue = null;
    
    /** @var ActiveRecord */
    private $model = null;
    
    /**
     * @param string $name
     * @return self
     */
    public static function define($name, $defination=null) {
        $attribute = new Attribute();
        $attribute->name = $name;
        if (null === $defination) {
            # nothing
        } else if ( is_string($defination) ) {
            $attribute->setupDefinationByString($defination);
        } else {
            throw new DatabaseException('unsupported active record attribute defination');
        }
        return $attribute;
    }
    
    /**
     * @param string $defination
     * @return void
     */
    private function setupDefinationByString( $defination ) {
        $defination = array_filter(str_getcsv($defination,' ', ""));
        while ( null !== ( $item=array_pop($defination) ) ) {
            $syntax = strtoupper($item);
            if ( 'PRIMARY_KEY' === $syntax ) {
                $this->setIsPrimaryKey(true);
            } else if ( 'AUTO_INCREASE' === $syntax ) {
                $this->setIsAutoIncrement(true);
            } else if ( ('[' === $item[0]) && (']' === $item[strlen($item)-1]) ) {
                $this->setDefaultVal(substr($item, 1, strlen($item)-2));
            } else if ( false !== ($validator = Validator::formatAsBuildInValidator($syntax)) ) {
                $this->addValidator($validator);
            } else  {
                $type = explode('(', $syntax);
                $this->setType($type[0]);
                if ( isset($type[1]) ) {
                    $this->setLength(substr($type[1], 0, strlen($type[1])-1));
                }
            }
        }
    }
    
    /** */
    public function __construct() {
        $this->validator = new Validator($this);
    }
    
    /** @return \X\Service\Database\ActiveRecord */
    public function getModel() {
        return $this->model;
    }
    
    /** @return self */
    public function setModel( ActiveRecord $model ) {
        $this->model = $model;
        return $this;
    }
    
    /** @return string */
    public function getName() {
        return $this->name;
    }
    
    /** @return self */
    public function setIsPrimaryKey( $isPrimaryKey ) {
        $this->isPrimaryKey = true;
        return $this;
    }
    
    /** @return self */
    public function setIsAutoIncrement( $isAutoIncrement ) {
        $this->isAutoIncrement = true;
        return $this;
    }
    
    /** @return self */
    public function setDefaultVal( $value ) {
        $this->defaultVal = $value;
        return $this;
    }
    
    /** @return self */
    public function addValidator( $validator ) {
        $this->validator->addValidator($validator);
        return $this;
    }
    
    /** @return self */
    public function setType( $type ) {
        $this->type = $type;
        return $this;
    }
    
    /** @return self */
    public function setLength( $length ) {
        $this->length = $length;
        return $this;
    }
    
    /** @return self */
    public function setValue( $value ) {
        $this->value = $value;
        return $this;
    }
    
    /** @return mixed */
    public function getValue() {
        $value = $this->value;
        if ( null === $value ) {
            $value = $this->defaultVal;
        }
        return $value;
    }
    
    /** @return boolean */
    public function getIsDirty() {
        return $this->oldValue !== $this->getValue();
    }
    
    /** @return boolean */
    public function getIsAutoIncrement() {
        return $this->isAutoIncrement;
    }
    
    /** @return self */
    public function setOldValue( $oldValue ) {
        $this->oldValue = $oldValue;
        return $this;
    }
    
    /** @return bool */
    public function getIsPrimaryKey() {
        return $this->isPrimaryKey;
    }
    
    /** @return bool */
    public function validate() {
        return $this->validator->validate();
    }
}