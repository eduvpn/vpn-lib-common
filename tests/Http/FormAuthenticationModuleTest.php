<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit_Framework_TestCase;
use SURFnet\VPN\Common\Http\FormAuthenticationModule;
use SURFnet\VPN\Common\Http\Service;
use SURFnet\VPN\Common\Tests\TestTpl;

class FormAuthenticationModuleTest extends PHPUnit_Framework_TestCase
{
    public function testVerifyCorrect()
    {
        $session = new TestSession();
        $tpl = new TestTpl();
        $service = new Service();
        $formAuthenticationModule = new FormAuthenticationModule(
            [
                // foo:bar
                'foo' => '$2y$10$F4lt5FzX.wfr2s3jsTy9XuxU2T7J5R0bTnMbu.9MDjphIupbG54l6',
            ],
            $session,
            $tpl
        );
        $formAuthenticationModule->init($service);

        $request = new TestRequest(
            [
                'HTTP_REFERER' => 'http://vpn.example/account',
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/_form/auth/verify',
            ],
            [],
            [
                'userName' => 'foo',
                'userPass' => 'bar',
                '_form_auth_redirect_to' => 'http://vpn.example/account',
            ]
        );

        $response = $service->run($request);
        $this->assertSame('foo', $session->get('_form_auth_user'));
        $this->assertSame(302, $response->getStatusCode());
    }

    public function testVerifyIncorrect()
    {
        $session = new TestSession();
        $tpl = new TestTpl();

        $service = new Service();
        $formAuthenticationModule = new FormAuthenticationModule(
            [
                'foo' => 'bar',
            ],
            $session,
            $tpl
        );
        $formAuthenticationModule->init($service);

        $request = new TestRequest(
            [
                'HTTP_REFERER' => 'http://vpn.example/account',
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/_form/auth/verify',
            ],
            [],
            [
                'userName' => 'foo',
                'userPass' => 'baz',
                '_form_auth_redirect_to' => 'http://vpn.example/account',
            ]
        );

        $response = $service->run($request);
        $this->assertFalse($session->has('_form_auth_user'));

        $this->assertSame('{"formAuthentication":{"_form_auth_invalid_credentials":true,"_form_auth_invalid_credentials_user":"foo","_form_auth_redirect_to":"http:\/\/vpn.example\/account","_form_auth_login_page":true}}', $response->getBody());
    }
}
