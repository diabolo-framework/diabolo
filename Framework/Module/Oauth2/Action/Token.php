<?php
namespace X\Module\Oauth2\Action;
use X\Core\X;
use X\Service\OAuth2\Service as OAuth2Service;
use X\Module\Oauth2\Util\ActionBase;
class Token extends ActionBase {
    /**
     * {@inheritDoc}
     * @see \X\Module\Oauth2\Util\ActionBase::handle()
     */
    protected function handle() {
        /** @var $oauth2Service OAuth2Service */
        $oauth2Service = X::system()->getServiceManager()->get(OAuth2Service::getServiceName());
        $request = \OAuth2\Request::createFromGlobals();
        $oauth2Service->handleTokenRequest($request)->send();
    }
}