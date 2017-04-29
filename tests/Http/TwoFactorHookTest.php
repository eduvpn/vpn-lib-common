<?php
/**
 *  Copyright (C) 2016 SURFnet.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
