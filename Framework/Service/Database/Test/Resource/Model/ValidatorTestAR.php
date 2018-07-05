<?php
namespace X\Service\Database\Test\Resource\Model;
use X\Core\X;
use X\Service\Database\ActiveRecord;
use X\Service\Database\ActiveRecord\Attribute;
use X\Service\Database\Database;
class ValidatorTestAR extends ActiveRecord {
    /**
     * {@inheritDoc}
     * @see \X\Service\Database\ActiveRecord::getDefination()
     */
    protected function getDefination() {
        return array(
            Attribute::define('id')
                ->addValidator('idIsNumber')
                ->addValidator('notNull')
                ->addValidator('unique')
                ->addValidator(array($this, 'validateIdIsNumber')),
        );
    }
    
    /**
     * @param ActiveRecord $model
     * @param Attribute $attr
     */
    public function validateIdIsNumber( ActiveRecord $model, Attribute $attr ) {
        if ( !is_numeric($attr->getValue()) ) {
            $model->addError($attr->getName(), 'id is not a number');
        }
    }
    
    public static function getDB () {
        $config = X::system()->getConfiguration()->get('params')->get('MysqlDriverConfig');
        return new Database($config);
    }
    
    public static function tableName() {
        return 'students';
    }
}