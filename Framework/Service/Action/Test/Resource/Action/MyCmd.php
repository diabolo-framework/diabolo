<?php
namespace X\Service\Action\Test\Resource\Action;
use X\Service\Action\Handler\CommandAction;
class MyCmd extends CommandAction {
    /**
     * {@inheritDoc}
     * @see \X\Service\Action\Handler\ApiAction::run()
     */
    protected function run( ) {
        $data = array();
        do {
            $data['readline'] = $this->readLine("input 'readline' : ");
        } while ('readline' !== $data['readline'] );
        
        do {
            $data['readchar'] = $this->readChar("nput 'r' : ");
        } while ( 'r' !== $data['readchar'] );
        
        do {
            $data['promit_default'] = $this->prompt("prompt default test, no input", 'DEFAULT_CONTENT');
        } while ( 'DEFAULT_CONTENT' !== $data['promit_default'] );
        
        do {
            $data['confirm_default'] = $this->confirm('confirm default test, no input', true);
        } while ( true !== $data['confirm_default'] );
        
        do {
            $data['select_default'] = $this->select('select default test, no input', array('red','blue','yellow'), 'blue');
        } while( 1 !== $data['select_default'] );
        
        do {
            $data['prompt'] = $this->prompt('input "prompt"');
        } while ( 'prompt' !== $data['prompt'] );
        
        do {
            $data['confirm'] = $this->confirm('confirm for "y"');
        } while ( true !== $data['confirm'] );
        
        do {
            $data['select'] = $this->select('select "yellow"', array('red','blue','yellow'));
        } while( 2 !== $data['select'] );
        return $data;
    }
}