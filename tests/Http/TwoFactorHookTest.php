<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Tests\Http;

use DateTime;
use LetsConnect\Common\Http\TwoFactorHook;
use LetsConnect\Common\Http\UserInfo;
use LetsConnect\Common\HttpClient\ServerClient;
use LetsConnect\Common\Tests\TestTpl;
use PHPUnit\Framework\TestCase;

class TwoFactorHookTest extends TestCase
{
    public function testAlreadyVerified()
    {
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');
        $session = new TestSession();
        $session->set('_two_factor_verified', 'foo');
        $tpl = new TestTpl();
        $formAuthentication = new TwoFactorHook($session, $tpl, $serverClient, false);
        $request = new TestRequest([]);
        $this->assertTrue($formAuthentication->executeBefore($request, ['auth' => new UserInfo('foo', [], new DateTime())]));
    }

    public function testNotRequiredEnrolled()
    {
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');
        $session = new TestSession();
        $tpl = new TestTpl();
        $formAuthentication = new TwoFactorHook($session, $tpl, $serverClient, false);
        $request = new TestRequest([]);
        $response = $formAuthentication->executeBefore($request, ['auth' => new UserInfo('foo', [], new DateTime())]);
        $this->assertSame('{"twoFactorTotp":{"_two_factor_user_id":"foo","_two_factor_auth_invalid":false,"_two_factor_auth_redirect_to":"http:\/\/vpn.example\/"}}', $response->getBody());
    }

    public function testNotRequiredNotEnrolled()
    {
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');
        $session = new TestSession();
        $tpl = new TestTpl();
        $formAuthentication = new TwoFactorHook($session, $tpl, $serverClient, false);
        $request = new TestRequest([]);
        $this->assertTrue($formAuthentication->executeBefore($request, ['auth' => new UserInfo('bar', [], new DateTime())]));
    }

    public function testRequireTwoFactorNotEnrolled()
    {
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');
        $session = new TestSession();
        $tpl = new TestTpl();
        $formAuthentication = new TwoFactorHook($session, $tpl, $serverClient, true);
        $request = new TestRequest([]);
        $response = $formAuthentication->executeBefore($request, ['auth' => new UserInfo('bar', [], new DateTime())]);
        $this->assertSame('http://vpn.example/', $session->get('_two_factor_enroll_redirect_to'));
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://vpn.example/two_factor_enroll', $response->getHeader('Location'));
    }

    /**
     * @expectedException \LetsConnect\Common\Http\Exception\HttpException
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
        $formAuthentication = new TwoFactorHook($session, $tpl, $serverClient, false);
        $request = new TestRequest([]);
        $this->assertTrue($formAuthentication->executeBefore($request, ['auth' => new UserInfo('foo', [], new DateTime())]));
    }
}
