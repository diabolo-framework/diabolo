<?php
namespace X\Service\Action\Test\Resource\Action;
use X\Service\Action\Handler\AjaxAction;
class MyAjax extends AjaxAction {
    /**
     * {@inheritDoc}
     * @see \X\Service\Action\Handler\ApiAction::run()
     */
    protected function run( $account, $password ) {
        if ( 'account'===$account && 'password'===$password ) {
            $this->success(array('uid'=>1));
        } else if ( 'admin' === $account ) {
            $this->error('admin is not allowed');
        } else if ( 'demo' === $account) {
            $this->response(array('demo'=>'demo'));
        } else if ('text' === $account )  {
            $this->responseFormat = self::FMT_TEXT;
            $this->success('TEXT');
        } else {
            throw new \Exception('unknown account');
        }
    }
}