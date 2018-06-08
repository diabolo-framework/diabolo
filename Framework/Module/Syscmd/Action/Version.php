<?php
namespace X\Module\Syscmd\Action;
use X\Service\XAction\Handler\CommandAction;
class Version extends CommandAction {
    /**
     * {@inheritDoc}
     * @see \X\Service\XAction\Util\Action::runAction()
     */
    public function runAction() {
        echo "1.0.0\n";
    }
}