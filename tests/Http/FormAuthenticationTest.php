<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Tests\Http;

use LC\Common\Http\FormAuthentication;
use LC\Common\Http\Service;
use LC\Common\Http\SimpleAuth;
use LC\Common\Tests\TestTpl;
use PHPUnit\Framework\TestCase;

class FormAuthenticationTest extends TestCase
{
    /**
     * @return void
     */
    public function testAuthenticated()
    {
        $session = new TestSession();
        $session->set('_form_auth_user', 'foo');
        $session->set('_form_auth_permission_list', serialize(['foo']));

        $tpl = new TestTpl();
        $formAuthentication = new FormAuthentication(
            new SimpleAuth(
                [
                    // foo:bar
                    'foo' => '$2y$10$F4lt5FzX.wfr2s3jsTy9XuxU2T7J5R0bTnMbu.9MDjphIupbG54l6',
                ]
            ),
            $session,
            $tpl
        );

        $request = new TestRequest([]);

        $this->assertSame('foo', $formAuthentication->executeBefore($request, [])->getUserId());
    }

    /**
     * @return void
     */
    public function testNotAuthenticated()
    {
        $session = new TestSession();
        $tpl = new TestTpl();
        $formAuthentication = new FormAuthentication(
            new SimpleAuth(
                [
                    // foo:bar
                    'foo' => '$2y$10$F4lt5FzX.wfr2s3jsTy9XuxU2T7J5R0bTnMbu.9MDjphIupbG54l6',
                ]
            ),
            $session,
            $tpl
        );

        $request = new TestRequest(
            [
            ]
        );

        $response = $formAuthentication->executeBefore($request, []);
        $this->assertSame('{"formAuthentication":{"_form_auth_invalid_credentials":false,"_form_auth_redirect_to":"http:\/\/vpn.example\/","_show_logout_button":false}}', $response->getBody());
    }

    /**
     * @return void
     */
    public function testVerifyCorrect()
    {
        $session = new TestSession();
        $tpl = new TestTpl();
        $service = new Service();
        $formAuthentication = new FormAuthentication(
            new SimpleAuth(
                [
                    // foo:bar
                    'foo' => '$2y$10$F4lt5FzX.wfr2s3jsTy9XuxU2T7J5R0bTnMbu.9MDjphIupbG54l6',
                ]
            ),
            $session,
            $tpl
        );
        $formAuthentication->init($service);

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
        $this->assertSame(serialize(null), $session->get('_form_auth_session_expires_at'));
        $this->assertSame(302, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testVerifyIncorrect()
    {
        $session = new TestSession();
        $tpl = new TestTpl();

        $service = new Service();
        $formAuthentication = new FormAuthentication(
            new SimpleAuth(
                [
                    'foo' => 'bar',
                ]
            ),
            $session,
            $tpl
        );
        $formAuthentication->init($service);

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
        $this->assertNull($session->get('_form_auth_user'));

        $this->assertSame('{"formAuthentication":{"_form_auth_invalid_credentials":true,"_form_auth_invalid_credentials_user":"foo","_form_auth_redirect_to":"http:\/\/vpn.example\/account","_show_logout_button":false}}', $response->getBody());
    }
}
