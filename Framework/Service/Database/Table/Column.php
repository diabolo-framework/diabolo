<?php
namespace X\Service\Database\Table;
use X\Service\Database\Database;

class Column {
    const T_STRING = 'STRING';
    const T_INTEGER = 'INTEGER';
    const T_DECIMAL = 'DECIMAL';
    
    const T_TINYINT = 'TINYINT';
    const T_SMALLINT = 'SMALLINT';
    const T_MEDIUMINT = 'MEDIUMINT';
    const T_INT = 'INT';
    const T_BIGINT = 'BIGINT';
    const T_DOUBLE = 'DOUBLE';
    const T_FLOAT = 'FLOAT';
    const T_DATE = 'DATE';
    const T_TIME = 'TIME';
    const T_DATETIME = 'DATETIME';
    const T_CHAR = 'CHAR';
    const T_VARCHAR = 'VARCHAR';
    const T_TINYTEXT = 'TINYTEXT';
    const T_TEXT = 'TEXT';
    const T_MEDIUMTEXT = 'MEDIUMTEXT';
    const T_LONGTEXT = 'LONGTEXT';
    
    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var int */
    private $length = null;
    /** @var int */
    private $decimals;
    /** @var boolean */
    private $isNotNull;
    /** @var mixed */
    private $defaultValue;
    /** @var boolean */
    private $isAutoIncrement;
    /** @var boolean */
    private $isUnique;
    /** @var boolean */
    private $isPrimary;
    /** @var string */
    private $comment;
    
    /** @var string */
    private $newColumnAfter;
    /** @var Database */
    private $db= null;
    
    /** @return self */
    public static function build() {
        return new static();
    }
    
    /**
     * @param Database $db
     * @return self
     */
    public function setDatabase( Database $db ) {
        $this->db = $db;
        return $this;
    }
    
    /**
     * @param unknown $name
     * @return self
     */
    public function setName( $name ) {
        $this->name = $name;
        return $this;
    }
    
    /** @return string */
    public function getName() {
        return $this->name;
    }
    
    /**
     * @param unknown $type
     * @return self
     */
    public function setType( $type  ) {
        $this->type = strtoupper($type);
        return $this;
    }
    
    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * @param unknown $length
     * @return self
     */
    public function setLength( $length ) {
        $this->length = $length;
        return $this;
    }
    
    /**
     * @return number
     */
    public function getLength() {
        return $this->length;
    }
    
    /**
     * @param unknown $length
     * @return self
     */
    public function setDecimals( $length ) {
        $this->decimals = $length;
        return $this;
    }
    
    /**
     * @param unknown $isNotNull
     * @return self
     */
    public function setIsNotNull( $isNotNull ) {
        $this->isNotNull = $isNotNull;
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsNotNull() {
        return $this->isNotNull;
    }
    
    /**
     * @param unknown $defaultValue
     * @return self
     */
    public function setDefaultValue( $defaultValue ) {
        $this->defaultValue = $defaultValue;
        return $this;
    }
    
    /**
     * @return mixed|unknown
     */
    public function getDefaultValue() {
        return $this->defaultValue;
    }
    
    /**
     * @param unknown $isAutoIncrement
     * @return self
     */
    public function setIsAutoIncrement( $isAutoIncrement ) {
        $this->isAutoIncrement = $isAutoIncrement;
        return $this;
    }
    
    /***
     * @return boolean
     */
    public function getIsAutoIncrement() {
        return $this->isAutoIncrement;
    }
    
    /**
     * @param unknown $isUnique
     * @return self
     */
    public function setIsUnique( $isUnique ) {
        $this->isUnique = $isUnique;
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsUnique() {
        return $this->isUnique;
    }
    
    /**
     * @param unknown $isPrimary
     * @return self
     */
    public function setIsPrimary( $isPrimary ) {
        $this->isPrimary = $isPrimary;
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsPrimary() {
        return $this->isPrimary;
    }
    
    /**
     * @param unknown $comment
     * @return self
     */
    public function setComment( $comment ) {
        $this->comment = $comment;
        return $this;
    }
    
    /**
     * @param unknown $columnName
     * @return self
     */
    public function setAfterColumn( $columnName ) {
        $this->newColumnAfter = $columnName;
        return $this;
    }
    
    /** @return string */
    public function toString() {
        $column = array();
        if ( !empty($this->name) ) {
            $column[] = $this->db->quoteColumnName($this->name);
        }
        
        $type = $this->db->getDriver()->mapColumnTypeToDatabaseType($this->type);
        switch ( $type ) {
        case self::T_CHAR :
        case self::T_VARCHAR : 
            if ( null === $this->length ) {
                $this->length = 255;
            }
            $column[] = "{$type}({$this->length})";
            break;
        case self::T_DOUBLE :
        case self::T_FLOAT : $column[] = "{$type}({$this->length},{$this->decimals})"; break;
        default: $column[] = $type; break;
        }
       
        if ( $this->isNotNull ) {
            $column[] = 'NOT NULL';
        }
        if ( $this->isAutoIncrement) {
            $column[] = 'AUTO_INCREMENT';
        }
        if ( $this->isPrimary ) {
            $column[] = 'PRIMARY KEY';
        }
        if ( $this->isUnique ) {
            $column[] = 'UNIQUE KEY';
        }
        if ( !empty($this->defaultValue) ) {
            $column[] = 'DEFAULT '.$this->db->quoteValue($this->defaultValue);
        }
        if ( !empty($this->comment) ) {
            $column[] = 'COMMENT '.$this->db->quoteValue($this->comment);
        }
        if ( !empty($this->newColumnAfter) ) {
            $column[] = 'AFTER '.$this->db->quoteColumnName($this->newColumnAfter);
        }
        return implode(' ', $column);
    }
    
    /** @return string */
    public function __toString() {
        return $this->toString();
    }
}