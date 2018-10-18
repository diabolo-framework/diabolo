<?php
namespace X\Service\Database;
use X\Service\Database\ActiveRecord\Validator;
use X\Service\Database\ActiveRecord\Attribute;
use X\Service\Database\Query\Condition;
use X\Core\Component\Stringx;

abstract class ActiveRecord implements \JsonSerializable {
    /** default database connection name configured in service */
    const DB_DEFAULT_NAME = 'default';
    const REL_HAS_ONE = 1;
    const REL_HAS_MANY = 2;
    const REL_MANY_TO_MANY = 3;
    const REL_BELONGS = 4;
    
    /**
     * table column cache
     * @var array
     */
    private static $columnCache = array(
        # 'className' => array()
    );
    
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
    /** @var array */
    private $relations = array();
    
    /** setup the ar */
    public function __construct() {
        $this->setupDefination();
        $this->relations = $this->getRelations();
        
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
    protected function getDefination() {
        $cacheKey = get_class($this);
        
        $attributes = array();
        if ( !isset(self::$columnCache[$cacheKey]) ) {
            $columns = $this->getDatabase()->columnList(self::tableName());
            self::$columnCache[$cacheKey] = $columns;
        }
        
        $columns = self::$columnCache[$cacheKey];
        foreach ( $columns as $column ) {
            $attributes[] = Attribute::loadFromTableColumn($column);
        }
        return $attributes;
    }
    
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
     * @param array $data
     * @return self
     */
    public function setValues( array $data ) {
        foreach ( $data as $key => $value ) {
            $this->set($key, $value);
        }
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function set( $name, $value ) {
        $setter = 'set'.ucfirst($name);
        if ( method_exists($this, $setter) ) {
            $this->$setter($value);
            return $this;
        }
        
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
        $getter = 'get'.ucfirst($name);
        if ( method_exists($this, $getter) ) {
            $this->$getter();
            return $this;
        }
        
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
        $dirtyValues = $this->getDirtyValues();
        if ( empty($dirtyValues) ) {
            return true;
        }
        
        $qurey = Query::update($this->getDatabase())
            ->table(static::tableName())
            ->values($dirtyValues)
            ->where($this->getOperationCondition());
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
     * @return \X\Service\Database\Query\Condition
     */
    public function getExceptCondition() {
        $condition = Condition::build();
        if ( null !== $this->primaryKeyAttrName ) {
            $pkName = $this->primaryKeyAttrName;
            $condition->isNot($pkName, $this->get($pkName));
        } else {
            $values = $this->toArray();
            foreach ( $values as $key => $value ) {
                $condition->isNot($key, $value);
            }
        }
        return $condition;
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
    public function toArray() {
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
    
    /** 
     * get the database that current ar is using, as default,
     * it returns the default database name, you can overwrite
     * this method to custom the database that this ar is going
     * to use, also, you can return the database directly in this
     * method, it's allowed but not suggested.
     * @return string|Database 
     * */
    public static function getDB() {
        return self::DB_DEFAULT_NAME;
    }
    
    /** 
     * get table name of the active record, as default the table 
     * name get from class's name and convert it into snake case 
     * if not defined, you can overwrite this method to set table 
     * name.
     * @return string 
     * */
    public static function tableName() {
        $name = get_called_class();
        if ( false !== strpos($name, '\\') ) {
            $name = substr($name, strrpos($name, '\\')+1);
        }
        $name = Stringx::camelToSnake($name);
        return $name;
    }
    
    /** @return \X\Service\Database\Query\Select */
    public static function find() {
        $className = get_called_class();
        $query = Query::select(static::getDB());
        
        $query->from(static::tableName());
        $query->setReleatedActiveRecord($className);
        $query->setFetchStyle(QueryResult::FETCH_CLASS);
        $query->setFetchClass($className);
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
     * @param unknown $name
     * @return mixed
     */
    public static function getFilter( $name ) {
        $filter = array(get_called_class(), 'filter'.ucfirst($name));
        if ( is_callable($filter) ) {
            return call_user_func($filter);
        } else if ( 'default' === $name ) {
            return null;
        } else {
            throw new DatabaseException("unable to find filter `{$name}`");
        }
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
    
    /** @return string */
    public static function getPrimaryKeyName() {
        $model = new static();
        return $model->primaryKeyAttrName;
    }
    
    /**
     * @return array
     */
    protected function getRelations() {
        return array();
    }
    
    /**
     * @param unknown $className
     * @param unknown $relKeyName
     * @return self
     */
    protected function relationHasOne ( $name, $className, $relKeyName ) {
        $this->relations[$name] = array(
            'type' => self::REL_HAS_ONE,
            'class' => $className,
            'key' => $relKeyName,
        );
        return $this;
    }
    
    /**
     * @param unknown $className
     * @param unknown $relKeyName
     * @return self
     */
    protected function relationHasMany( $name, $className, $relKeyName ) {
        $this->relations[$name] = array(
            'type' => self::REL_HAS_MANY,
            'class' => $className,
            'key' => $relKeyName,
        );
        return $this;
    }
    
    /**
     * @param unknown $className
     * @param unknown $relKeyName
     * @return self
     */
    protected function relationBelongs ( $name, $className, $relKeyName ) {
        $this->relations[$name] = array(
            'type' => self::REL_BELONGS,
            'class' => $className,
            'key' => $relKeyName,
        );
        return $this;
    }
    
    /**
     * @param unknown $targetClassName
     * @param unknown $targetKeyName
     * @param unknown $selfKeyName
     * @param unknown $mapClassName
     * @return self
     */
    protected function relationManyToMany( 
        $name,
        $targetClassName, 
        $targetKeyName, 
        $selfKeyName, 
        $mapClassName 
    ) {
        $this->relations[$name] = array(
            'type' => self::REL_MANY_TO_MANY,
            'targetClass' => $targetClassName,
            'targetKey' => $targetKeyName,
            'selfKey' => $selfKeyName,
            'mapClass' => $mapClassName,
        );
        return $this;
    }
    
    /**
     * @param unknown $relationName
     * @return self|self[]|false
     */
    private function getReleatedRecordsByCaller( $relationCallerName ) {
        $relationName = lcfirst(substr($relationCallerName, 3));
        if ( !isset($this->relations[$relationName]) ) {
            return false;
        }
        
        $relation = $this->relations[$relationName];
        switch ( $relation['type'] ) {
        case self::REL_HAS_ONE : 
            $className = $relation['class'];
            $pkName = $className::getPrimaryKeyName();
            return $className::findOne([$relation['key'] => $this->get($this->primaryKeyAttrName)]);
        case self::REL_HAS_MANY : 
            $className = $relation['class'];
            return $className::findAll([$relation['key'] => $this->get($this->primaryKeyAttrName)]);
        case self::REL_BELONGS : 
            $className = $relation['class'];
            $pkName = $className::getPrimaryKeyName();
            return $className::findOne([$pkName=>$this->get($relation['key'])]);
        case self::REL_MANY_TO_MANY :
            $mapClassName = $relation['mapClass'];
            $className = $relation['targetClass'];
            $pkName = $className::getPrimaryKeyName();
            return $className::find()
                ->where(array(
                    $pkName => Query::select($mapClassName::getDb())
                        ->expression($relation['targetKey'])
                        ->from($mapClassName::tableName())
                        ->where(array($relation['selfKey']=>$this->get($this->primaryKeyAttrName)))
                ))
                ->all();
        default :
            throw new DatabaseException("unsupported relation type `{$relation['type']}`");
        }
    }
    
    /**
     * @param unknown $name
     * @param unknown $params
     */
    public function __call( $name, $params ) {
        $relationData = $this->getReleatedRecordsByCaller($name);
        if ( false !== $relationData ) {
            return $relationData;
        }
        throw new DatabaseException("call to undefined method `{$name}`");
    }
    
    /**
     * {@inheritDoc}
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize () {
        return $this->toArray();
    }
}