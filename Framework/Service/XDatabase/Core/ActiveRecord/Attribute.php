<?php
/**
 * This file is part of x-database service.
 * @license LGPL http://www.gnu.de/documents/lgpl.en.html
 */
namespace X\Service\XDatabase\Core\ActiveRecord;

/**
 * 
 */
use X\Core\X;
use X\Service\XDatabase\Core\Table\Column as TableColumn;
use X\Service\XDatabase\Core\SQL\Util\Expression;

/**
 * Active record's attribute
 * @author Michael Luthor <michaelluthro@163.com>
 */
class Attribute extends TableColumn {
    /**
     * setup this attribuite by description string
     * @param string $description
     */
    public function setupByString( $description ) {
        $description = str_getcsv($description, ' ', '"');
        while ( null !== ( $item=array_pop($description) ) ) {
            switch ( strtoupper($item) ) {
            case 'PRIMARY'  : $this->setIsPrimaryKey(true); break;
            case 'UNSIGNED' : $this->setIsUnsigned(true); break;
            case 'UNIQUE'   : $this->setIsUnique(true); break;
            case 'NOTNULL'  : $this->setNullable(false); break;
            case 'AUTO_INCREASE' : $this->setIsAutoIncrement(true); break;
            default:
                if ( ('[' === $item[0]) && (']' === $item[strlen($item)-1]) ) {
                    $this->setDefault(substr($item, 1, strlen($item)-2));
                } else {
                    $type = explode('(', $item);
                    $this->setType($type[0]);
                    if ( isset($type[1]) ) {
                        $this->setLength(substr($type[1], 0, strlen($type[1])-1));
                    }
                }
                break;
            }
        }
    }
    
    /**
     * this value hold the record that this attribute belongs to.
     * @var \X\Service\XDatabase\Core\ActiveRecord\XActiveRecord
     */
    protected $record = null;
    
    /**
     * set record for this attribtue.
     * @param XActiveRecord $record
     * @return \X\Service\XDatabase\Core\ActiveRecord\Column
     */
    public function setRecord( $record ) {
        $this->record = $record;
        return $this;
    }
    
    /**
     * this value contains exten attributes.
     * @var array
     */
    protected $attachAttributes = array(
        'minLength'     => null,
        'isUnique'      => false,
    );
    
    /**
     * construce this attribute.
     * @param string $name
     */
    public function __construct($name) {
        $this->attributes = array_merge($this->attributes, $this->attachAttributes);
        parent::__construct($name);
    }
    
    /**
     * set min length of this attribute
     * @param integer $length
     * @return \X\Service\XDatabase\Core\ActiveRecord\Column
     */
    public function setMinLength( $length ) {
        return $this->set('minLength', (int)$length);
    }
    
    /**
     * get min length of this attribute.
     * @return Ambigous <\X\Service\XDatabase\Core\Table\mixed, multitype:>
     */
    public function getMinLength() {
        return $this->get('minLength');
    }
    
    /**
     * set is unique of this attribute.
     * @param boolean $isUnique
     * @return \X\Service\XDatabase\Core\ActiveRecord\Column
     */
    public function setIsUnique($isUnique) {
        return $this->set('isUnique', $isUnique);
    }
    
    /**
     * get is unique of this attribute.
     * @return Ambigous <\X\Service\XDatabase\Core\Table\mixed, multitype:>
     */
    public function getIsUnique() {
        return $this->get('isUnique');
    }
    
    /**
     * this value hold the value builder for this attribute.
     * @var mixed
     */
    protected $valueBuilder = null;
    
    /**
     * set value builder for this attribute.
     * @param callback $builder
     * @return \X\Service\XDatabase\Core\ActiveRecord\Column
     */
    public function setValueBuilder( $builder ) {
        if ( is_string($builder) && method_exists($this->record, $builder) ) {
            $builder = array($this->record, $builder);
        }
        
        $this->valueBuilder = $builder;
        return $this;
    }
    
    /**
     * new value of this attribute
     * @var mixed
     */
    protected $newValue = null;
    
    /**
     * old value of this attribute
     * @var mixed
     */
    protected $oldValue = null;
    
    /**
     * set old value for this attribute
     * @param mixed $oldValue
     * @return \X\Service\XDatabase\Core\ActiveRecord\Attribute
     */
    public function setOldValue( $oldValue ) {
        $this->oldValue = $oldValue;
        return $this;
    }
    
    /** @return mixed */
    public function getOldValue() {
        return $this->oldValue;
    }
    
    /**
     * set value for this attribute
     * @param mixed $value
     * @return \X\Service\XDatabase\Core\ActiveRecord\Column
     */
    public function setValue( $value ) {
        $this->newValue = $value;
        return $this;
    }
    
    /**
     * Get value from Column object.
     * @return mixed
     */
    public function getValue() {
        $value = null;
        if ( null !== $this->newValue ) {
            $value = $this->newValue;
        } else if ( null !== $this->valueBuilder && is_callable($this->valueBuilder) ) {
            $value = call_user_func_array($this->valueBuilder, array($this->record, $this->getName()));
        } else {
            $value = $this->getDefault();
        }
        $this->newValue = $value;
        return $value;
    }
    
    /**
     * Get whether the value of Column has been modified.
     * @return boolean
     */
    public function getIsDirty() {
        return $this->oldValue !== $this->newValue;
    }
    
    /**
     * Clean the dirty status, set the value to new value.
     * @return Column
     */
    public function cleanDirty() {
        $this->newValue = $this->oldValue;
        return $this;
    }
    
    /**
     * This value contains all errors on current Column object. 
     * @var array
     */
    protected $errors = array();
    
    /**
     * Add error to current Column object.
     * @param string $message
     * @return Column
     */
    public function addError( $message ) {
        $this->errors[] = call_user_func_array('sprintf', func_get_args());
        return $this;
    }
    
    /**
     * Get whether there is an error on current Column obejct.
     * @return boolean
     */
    public function hasError() {
        return 0 < count($this->errors);
    }
    
    /**
     * Get the errors on current Column object.
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * This value contains all custome validators for current Column object.
     * @var callback[]
     */
    protected $validators = array();
    
    /**
     * Add custome validator to current Column obejct.
     * @param callback $validator
     * @return Column
     */
    public function addValidator( $validator ) {
        $this->validators[] = $validator;
        return $this;
    }
    
    /**
     * Validate current Column object, and return validate result.
     * @return boolean
     */
    public function validate( $cleanError=true ) {
        if ( $cleanError ) {
            $this->errors = array();
        }
        
        if ( $this->getIsAutoIncrement() ) {
            return true;
        }
        
        $value = $this->getValue();
        $isValidated = true;
        $isValidated = $isValidated && $this->validateNotNull($value);
        $isValidated = $isValidated && $this->validateDataType($value);
        $isValidated = $isValidated && $this->validateLength($value);
        $isValidated = $isValidated && $this->validateUnique($value);
        $isValidated = $isValidated && $this->validatePrimaryKey($value);
        $isValidated = $isValidated && $this->validateUnsigned($value);
        if ( false === $isValidated ){
            return false;
        }
        
        $defaultValidator = new AttributeValidator();
        foreach ( $this->validators as $validator ) {
            if ( is_callable($validator) ) {
                call_user_func_array($validator, array($this->record, $this));
            } else {
                $defaultValidator->$validator($this->record, $this);
            }
        }
        
        return !$this->hasError();
    }
    
    /**
     * "NOT NULL" check for current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateNotNull( $value ) {
        if ( $this->getNullable() ) {
            return true;
        }
        
        if ( null === $value ) {
            $this->addError('%s不能为空。', $this->getLabel());
            return false;
        }
        return true;
    }
    
    /**
     * Validate the data type for current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateDataType( $value ) {
        if ( $value instanceof Expression ) {
            return true;
        }
        
        $handler = 'validateDataType'.ucfirst(strtolower($this->getType()));
        if ( method_exists($this, $handler) ) {
            return call_user_func_array(array($this, $handler), array($value));
        } else {
            $this->addError('Unknown type “%s” of Column “%s”.', $this->getType(), $this->getName());
            return false;
        }
    }
    
    /**
     * @param unknown $value
     * @return boolean
     */
    private function isInteger( $value ) {
        if ( is_numeric($value) ) {
            $value *= 1;
        }
        return is_int($value);
    }
    
    /**
     * Validate the data type integer for current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateDataTypeInt( $value ) {
        if ( !empty($value) && !$this->isInteger($value) ) {
            $this->addError('%s不是一个合法的整数。', $this->getLabel());
            return false;
        }
        return true;
    }
    
    /**
     * validate data type tiny int.
     * @param mixed $value
     * @return boolean
     */
    protected function validateDataTypeTinyint($value) {
        if (!empty($value)) {
            $isValidate = $this->isInteger($value);
            $isValidate = $isValidate && (($this->getIsUnsigned())?(0<$value&&$value<255):(-128<$value&&$value<128));
            if ( !$isValidate ){
                $this->addError('%s不是一个合法的整数。', $this->getLabel());
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate the data type varchar for current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateDataTypeVarchar( $value ) {
        $validate = null===$value && $this->getNullable();
        $validate = $validate || is_numeric($value);
        $validate = $validate || is_string($value);
        $validate = $validate || (is_object($value) && method_exists($value, '__toString'));
        
        if ( !$validate ) {
            $this->addError('%s不是一个合法的字符串。', $this->getLabel());
            return false;
        }
        return true;
    }
    
    /**
     * validate the data type for long text
     * @param mixed $value
     * @return boolean
     */
    protected function validateDataTypeLongtext( $value ) {
        return $this->validateDataTypeVarchar($value);
    }
    
    /**
     * validate the data type for long text
     * @param mixed $value
     * @return boolean
     */
    protected function validateDataTypeText( $value ) {
        return $this->validateDataTypeVarchar($value);
    }
    
    /**
     * Validate the data type for datetime current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateDataTypeDatetime( $value ) {
        if (!empty($value) 
        && false === \DateTime::createFromFormat('Y-m-d H:i:s', $value) 
        && false === \DateTime::createFromFormat('Y-m-d H:i', $value)
        && false === \DateTime::createFromFormat('Y-m-d', $value)) {
            $this->addError('%s不是一个合法的日期。', $this->getLabel());
            return false;
        }
        return true;
    }
    
    /**
     * Validate the data type for datetime current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateDataTypeDate( $value ) {
        if ( !empty($value) && false === \DateTime::createFromFormat('Y-m-d', $value) ) {
            $this->addError('The value of “%s” is not a validated date and time.', $this->getName());
            return false;
        }
        return true;
    }
    
    /**
     * Validate the data type for time current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateDataTypeTime( $value ) {
        if (!empty($value) && false === \DateTime::createFromFormat('H:i:s', $value) && false === \DateTime::createFromFormat('H:i', $value) ) {
            $this->addError('The value of “%s” is not a validated time.', $this->getName());
            return false;
        }
        return true;
    }
    
    /**
     * Validate the data length for current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateLength( $value ) {
        $type = strtoupper($this->getType());
        if ( 'VARCHAR' !== $type && 'CHAR' !== $type ) {
            return true;
        }
        
        $length = $this->getLength();
        $valLength = mb_strlen($value, 'UTF-8');
        if ( null !== $length && $valLength > $length ) {
            $overLength = $valLength - $length;
            $this->addError('%s超出%d个字符。', $this->getLabel(), $overLength);
            return false;
        }
        
        $minLength = $this->getMinLength();
        if ( null !== $minLength && $valLength < $minLength ) {
            $this->addError('The value “%s” of "%s" is too short.', $value, $this->getName());
            return false;
        }
        
        return true;
    }
    
    /**
     * Unique check for current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateUnique( $value ) {
        if ( !$this->record->getIsNew() && !$this->getIsDirty() ) {
            return true;
        }
        if( $this->getIsUnique() && $this->record->exists(array($this->getName()=>$value)) ) {
            $this->addError('%s已经存在。',$this->getLabel());
            return false;
        }
        return true;
    }
    
    /**
     * Primary key check for current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validatePrimaryKey( $value ) {
        if( $this->getIsPrimaryKey()
        &&  $this->getIsDirty() 
        &&  $this->record->exists(array($this->getName()=>$value)) ) {
            $this->addError('The key “%s” of "%s" already exists.', $value, get_class($this->record));
            return false;
        }
        
        return true;
    }
    
    /**
     * Unsigned check for current Column object.
     * @param mixed $value
     * @return boolean
     */
    protected function validateUnsigned( $value ){
        $unsignedTypes = array('TINYINT', 'SMALLINT','INT', 'BIGINT');
        $type = strtoupper($this->getType());
        if ( $this->getIsUnsigned() && in_array($type, $unsignedTypes) && 0>$value ) {
            $this->addError('The value “%s” of "%s" can not lower that 0.', $value, $this->getName());
            return false;
        }
        return true;
    }
    
    /**
     * @return string
     */
    private function getLabel() {
        return $this->record->getAttributeLabel($this->getName());
    }
}