<?php 
namespace X\Service\XAction\Operator\Action\Action;
use X\Service\XAction\Handler\WebPageAction;
class CreateWebAction extends WebPageAction {
    /**
     * {@inheritDoc}
     * @see \X\Service\XAction\Util\Action::runAction()
     */
    public function runAction() {
        $this->addParticle('CreateWebAction', array(
            'form' => array(),
            'success'=>null,
            'message'=>null,
        ));
        $this->display();
    }
}