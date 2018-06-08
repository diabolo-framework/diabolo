<?php
namespace X\Module\Syscmd\Action;
use X\Service\XAction\Handler\CommandAction;
class Help extends CommandAction {
    /**
     * {@inheritDoc}
     * @see \X\Service\XAction\Util\Action::runAction()
     */
    public function runAction( $target=null ) {
        echo "HELP\n";
        echo $target."\n";
    }
}