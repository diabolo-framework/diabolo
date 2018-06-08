<?php
namespace X\Module\Syscmd\Action\Module;
use X\Service\XAction\Handler\CommandAction;
class Create extends CommandAction {
    /**
     * {@inheritDoc}
     * @see \X\Service\XAction\Util\Action::runAction()
     */
    public function runAction( $name ) {
        echo "Module/creaqte\n";
        echo $name."\n";
    }
}