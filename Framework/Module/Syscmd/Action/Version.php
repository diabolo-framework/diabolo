<?php
namespace X\Module\Syscmd\Action;
use X\Service\XAction\Handler\CommandAction;
class Version extends CommandAction {
    /**
     * display the version of diabolo framework
     * @return void
     */
    public function runAction($ddd=11111) {
        echo "1.0.0\n";
    }
}