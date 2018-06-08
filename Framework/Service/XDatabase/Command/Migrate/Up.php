<?php
namespace X\Service\XDatabase\Command\Migrate;
use X\Service\XAction\Handler\CommandAction;
class Up extends CommandAction {
    /** @var integer */
    protected $limit = 0;
    
    /**
     * {@inheritDoc}
     * @see \X\Service\XAction\Util\Action::runAction()
     */
    protected function runAction( $target ) {
        echo $target."\n";
        echo $this->limit."\n";
    }
}