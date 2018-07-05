<?php
namespace X\Service\Database\Test\Resource\Model;
use X\Core\X;
use X\Service\Database\ActiveRecord;
use X\Service\Database\Database;
class Student extends ActiveRecord {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\ActiveRecord::getDefination()
     */
    protected function getDefination() {
        return array(
            'id' => 'INT  NOT_NULL  PRIMARY_KEY AUTO_INCREASE "[ssss ]"',
            'name','age'
        );
    }
    
    public static function getDB () {
        $config = X::system()->getConfiguration()->get('params')->get('MysqlDriverConfig');
        return new Database($config);
    }
    
    public static function getTable() {
        return 'students';
    }
}