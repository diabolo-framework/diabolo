<?php
namespace X\Service\Database\Test\Resource\Model;
use X\Service\Database\ActiveRecord;
use X\Service\Database\Query\Condition;
class Book extends ActiveRecord {
    /**
     * @return number[]
     */
    protected static function filterDefault() {
        return array('is_deleted'=>0);
    }
    
    /**
     * @return \X\Service\Database\Query\Condition
     */
    protected static function filterNotBorrowed() {
        return Condition::build()->isNot('is_borrowed', 1);
    }
}