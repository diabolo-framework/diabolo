<?php
namespace X\Service\XAction\Operator;
use X\Module\System\Component\ServiceWebOperator;
class Operator extends ServiceWebOperator {
    /**
     * {@inheritDoc}
     * @see \X\Module\System\Component\ServiceWebOperator::getOperations()
     */
    public function getOperations() {
        return array(
            array('name'=>'Create Web Action', 'action'=>'action/create-web-action'),
            array('name'=>'Create Ajax Action', 'action'=>'action/create-ajax-action'),
            array('name'=>'Create Command Action', 'action'=>'action/create-cli-action'),
            array('name'=>'Create API Action', 'action'=>'action/create-api-action'),
            array('name'=>'Create Web View', 'action'=>'view/create'),
            array('name'=>'Create Web View Widget', 'action'=>'widget/create'),
        );
    }
}