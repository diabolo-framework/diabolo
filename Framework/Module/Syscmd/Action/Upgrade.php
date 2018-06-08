<?php
namespace X\Module\Syscmd\Action;
use X\Service\XAction\Handler\CommandAction;
class Upgrade extends CommandAction {
    /**
     * {@inheritDoc}
     * @see \X\Service\XAction\Util\Action::runAction()
     */
    public function runAction() {
        echo "Upgrade\n";
    }
}