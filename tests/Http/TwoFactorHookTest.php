<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit_Framework_TestCase;
use SURFnet\VPN\Common\Http\TwoFactorHook;
use SURFnet\VPN\Common\HttpClient\ServerClient;
use SURFnet\VPN\Common\Tests\TestTpl;

class TwoFactorHookTest extends PHPUnit_Framework_TestCase
{
    public function testAuthenticated()
    {
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');
        $session = new TestSession();
        $session->set('_two_factor_verified', 'foo');
        $tpl = new TestTpl();
        $formAuthentication = new TwoFactorHook($session, $tpl, $serverClient);
        $request = new TestRequest([]);
        $this->assertTrue($formAuthentication->executeBefore($request, ['auth' => 'foo']));
    }

    public function testNotAuthenticatedEnrolled()
    {
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');
        $session = new TestSession();
        $tpl = new TestTpl();
        $formAuthentication = new TwoFactorHook($session, $tpl, $serverClient);
        $request = new TestRequest([]);
        $response = $formAuthentication->executeBefore($request, ['auth' => 'foo']);
        $this->assertSame('{"twoFactorTotp":{"_two_factor_auth_invalid":false,"_two_factor_auth_redirect_to":"http:\/\/vpn.example\/"}}', $response->getBody());
    }

    public function testNotAuthenticatedNotEnrolled()
    {
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');
        $session = new TestSession();
        $tpl = new TestTpl();
        $formAuthentication = new TwoFactorHook($session, $tpl, $serverClient);
        $request = new TestRequest([]);
        $this->assertFalse($formAuthentication->executeBefore($request, ['auth' => 'bar']));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage two-factor code not bound to authenticated user
     */
    public function testNotBoundToAuth()
    {
        // if you have access to two accounts using e.g. MellonAuth you could
        // use the cookie from one OTP-authenticated account in the other
        // without needing the OTP secret! So basically reducing the
        // authentication to one factor for the (admin) portal. This binding
        // makes sure that the authenticated user MUST be the same as the
        // one used for the two_factor verification
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');
        $session = new TestSession();
        $session->set('_two_factor_verified', 'bar');
        $tpl = new TestTpl();
        $formAuthentication = new TwoFactorHook($session, $tpl, $serverClient);
        $request = new TestRequest([]);
        $this->assertTrue($formAuthentication->executeBefore($request, ['auth' => 'foo']));
    }
}
