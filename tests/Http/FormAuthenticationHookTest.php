<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Tests\Http;

use LC\Common\Http\FormAuthenticationHook;
use LC\Common\Tests\TestTpl;
use PHPUnit\Framework\TestCase;

class FormAuthenticationHookTest extends TestCase
{
    /**
     * @return void
     */
    public function testAuthenticated()
    {
        $session = new TestSession();
        $session->set('_form_auth_user', 'foo');
        $session->set('_form_auth_permission_list', ['foo']);

        $tpl = new TestTpl();
        $formAuthentication = new FormAuthenticationHook($session, $tpl);

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
        $formAuthentication = new FormAuthenticationHook($session, $tpl);

        $request = new TestRequest(
            [
            ]
        );

        $response = $formAuthentication->executeBefore($request, []);
        $this->assertSame('{"formAuthentication":{"_form_auth_invalid_credentials":false,"_form_auth_redirect_to":"http:\/\/vpn.example\/","_form_auth_login_page":true}}', $response->getBody());
    }
}
