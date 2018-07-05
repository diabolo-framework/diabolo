<?php
namespace X\Service\Database;
use X\Service\Database\ActiveRecord\Validator;
use X\Service\Database\ActiveRecord\Attribute;
use X\Service\Database\Query\Condition;

abstract class ActiveRecord {
    /** @var Attribute[] */
    private $attributes = array();
    /** @var boolean */
    private $isNew = true;
    /** @var string */
    private $autoIncreamentAttrName = null;
    /** @var minxed */
    private $primaryKeyAttrName = null;
    /** @var Database */
    private $database = null;
    /** @var array */
    private $errors = array();
    
    /** setup the ar */
    public function __construct() {
        $this->setupDefination();
        $this->init();
    }
    
    /** setup attributes */
    private function setupDefination() {
        foreach ( $this->getDefination() as $name => $defination ) {
            if ( $defination instanceof Attribute ) {
                $name = $defination->getName();
                $attribute = $defination;
            } else {
                if ( is_numeric($name) ) {
                    $name = $defination;
                    $defination = null;
                }
                $attribute = Attribute::define($name, $defination);
            }
            
            $attribute->setModel($this);
            $this->attributes[$name] = $attribute;
            if ( $attribute->getIsAutoIncrement() ) {
                $this->autoIncreamentAttrName = $name;
            }
            if ( $attribute->getIsPrimaryKey() ) {
                $this->primaryKeyAttrName = $name;
            }
        }
    }
    
    /**  setup by submodel */
    protected function init( ) { }
    
    /**
     * define the atributes of this ar.
     * the key of array maps to name of attribute of record.
     * if the key is not a string, the value would be treated
     * as attribute name.
     * supported types are:
     * <li> string </li><li> int </li><li> decimal </li>
     * <li> date </li><li> time </li><li> datetime </li>
     * <li> text </li>
     * <br>
     * also some other key words are :
     * <li>({$num}) : after data type, specified the length of attr. </li>
     * <li>PRIMARY_KEY : mark as primary key </li>
     * <li>AUTO_INCREASE : mark as auto increase attr </li>
     * <li>[{$content}] : set default value </li>
     * <br>
     * you also able to pass validator name in definations,
     * and validator's name are name of methods in Validator
     * @see \X\Service\Database\ActiveRecord\Validator 
     * @example 
     * <pre>
     * return array(
     * 'id', 
     * 'name' => 'string(100) not-null', 
     * 'age')
     * </pre>
     * @return array
     */
    abstract protected function getDefination();
    
    /**
     * @param array $values
     * return self
     */
    public function applyData( $values ) {
        foreach ( $values as $name => $value ) {
            $this->getAttr($name)->setOldValue($value)->setValue($value);
        }
        $this->isNew = false;
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function set( $name, $value ) {
        $this->attributes[$name]->setValue($value);
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set( $name, $value ) {
        $this->set($name, $value);
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    public function get( $name ) {
        return $this->attributes[$name]->getValue();
    }
    
    /**
     * @param unknown $name
     * @return mixed
     */
    public function __get( $name ) {
        return $this->get($name);
    }
    
    /** @return boolean */
    public function getIsNew() {
        return $this->isNew;
    }
    
    /** @return bool */
    public function save() {
        if ( !$this->validate() ) {
            return false;
        }
        return $this->getIsNew() ? $this->insert() : $this->update();
    }
    
    /** @return boolean */
    private function insert() {
        $values = $this->getDirtyValues();
        $rowCount = Query::insert($this->getDatabase())
            ->table(static::tableName())
            ->value($values)
            ->exec();
        
        if ( 1 !== $rowCount ) {
            return false;
        }
        foreach ( $values as $attrName => $value ) {
            $this->attributes[$attrName]->setOldValue($value);
        }
        if ( null !== $this->autoIncreamentAttrName ) {
            $lastInsertValue = $this->getDatabase()->getLastInsertId();
            $this->attributes[$this->autoIncreamentAttrName]
                ->setValue($lastInsertValue)
                ->setOldValue($lastInsertValue);
        }
        
        $this->isNew = false;
        return true;
    }
    
    /** @return bool */
    private function update() {
        $qurey = Query::update($this->getDatabase())
            ->table(static::tableName())
            ->values($this->getDirtyValues())
            ->where($this->getOperationCondition())
            ->limit(1);
        return 1 === $qurey->exec();
    }
    
    /** @return array|Condition */
    private function getOperationCondition() {
        if ( null !== $this->primaryKeyAttrName ) {
            $pkName = $this->primaryKeyAttrName;
            return array($pkName => $this->attributes[$pkName]->getValue());
        } else {
            return $this->toArray();
        }
    }
    
    /**
     * @return mixed[]
     */
    private function getDirtyValues() {
        $values = array();
        foreach ( $this->attributes as $name => $attribute ) {
            if ( !$attribute->getIsDirty() ) {
                continue;
            }
            $values[$name] = $attribute->getValue();
        }
        return $values;
    }
    
    /** @return Database */
    private function getDatabase() {
        if ( null !== $this->database ) {
            return $this->database;
        }
        
        $db = static::getDB();
        if ( is_string($db) ) {
            $db = Service::getService()->getDB($db);
        }
        $this->database = $db;
        return $db;
    }
    
    /** @return array */
    private function toArray() {
        $values = array();
        foreach ( $this->attributes as $name => $attr ) {
            $values[$name] = $attr->getValue();
        }
        return $values;
    }
    
    /** @return boolean */
    public function delete() {
        if ( $this->getIsNew() ) {
            return true;
        }
        
        $rowCount = Query::delete($this->getDatabase())
            ->from(static::tableName())
            ->where($this->getOperationCondition())
            ->limit(1)
            ->exec();
        if ( 1 === $rowCount ) {
            $this->isNew = true;
        }
        return 1 === $rowCount;
    }
    
    /** @return boolean */
    public function validate( $attrName=null ) {
        $attributes = (null === $attrName) 
            ? $this->attributes 
            : array($this->attributes[$attrName]);
        
        foreach ( $this->attributes as $attribute ) {
            $attribute->validate();
        }
        return !$this->hasError();
    }
    
    /** @return boolean */
    public function hasError( $attrName=null ) {
        return (null === $attrName) 
        ? !empty($this->errors)
        : (isset($this->errors[$attrName]) && !empty($this->errors[$attrName])); 
    }
    
    /**
     * @param string $attrName
     * @return array|mixed|array
     */
    public function getErrors( $attrName=null ) {
        if ( null !== $attrName ) {
            return array_key_exists($attrName, $this->errors) 
            ? $this->errors[$attrName] 
            : array();
        } else {
            return $this->errors;
        }
    }
    
    /**
     * @param string $attrName
     * @param string $message
     * @return self
     */
    public function addError( $attrName, $message ) {
        $this->errors[$attrName][] = $message;
        return $this;
    }
    
    /**
     * @param string $name
     * @return \X\Service\Database\ActiveRecord\Attribute
     */
    public function getAttr( $name ) {
        return $this->attributes[$name];
    }
    
    /** @return string|Database */
    public static function getDB() {
        return 'db';
    }
    
    /** @return string */
    public static function tableName() {
        throw new DatabaseException('table name is not defined');
    }
    
    /** @return \X\Service\Database\Query\Select */
    public static function find() {
        $query = Query::select(static::getDB())
            ->from(static::tableName());
        $query->setFetchStyle(QueryResult::FETCH_CLASS);
        $query->setFetchClass(get_called_class());
        return $query;
    }
    
    /**
     * @param mixed $condition
     * @return self
     */
    public static function findOne( $condition=null ) {
        return static::find()->where($condition)->one();
    }
    
    /**
     * @param mixed $condition
     * @return self[]
     */
    public static function findAll( $condition=null ) {
        return static::find()->where($condition)->all();
    }
    
    /**
     * @param mixed $condition
     * @return number
     */
    public static function deleteAll($condition=null) {
        return Query::delete(static::getDB())
            ->from(static::tableName())
            ->where($condition)
            ->exec();
    }
    
    /**
     * @param array $values
     * @param mixed $condition
     * @return number
     */
    public static function updateAll( array $values, $condition=null ) {
        return Query::update(static::getDB())
            ->table(static::tableName())
            ->values($values)
            ->where($condition)
            ->exec();
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function has( $name ) {
        return isset($this->attributes[$name]);
    }
}