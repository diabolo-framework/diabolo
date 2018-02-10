<?php
namespace X\Service\XDatabase\Core\ActiveRecord;
class AttributeValidator {
    const NOT_EMPTY = 'validateNotEmpty';
    
    /**
     * @param XActiveRecord $model
     * @param Attribute $attr
     */
    public function validateNotEmpty( XActiveRecord $model, Attribute $attr ) {
        $value = $attr->getValue();
        if ( empty($value) ) {
            $attr->addError(sprintf('%s不能为空', $model->getAttributeLabel($attr->getName())));
        }
    }
}