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
use SURFnet\VPN\Common\Http\NullAuthenticationHook;
use SURFnet\VPN\Common\Http\Service;
use SURFnet\VPN\Common\Http\TwoFactorModule;
use SURFnet\VPN\Common\HttpClient\ServerClient;
use SURFnet\VPN\Common\Tests\TestTpl;

class TwoFactorModuleTest extends PHPUnit_Framework_TestCase
{
    public function testVerifyCorrect()
    {
        $session = new TestSession();
        $tpl = new TestTpl();
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');

        $service = new Service();
        $twoFactorModule = new TwoFactorModule(
            $serverClient,
            $session,
            $tpl
        );
        $service->addBeforeHook('auth', new NullAuthenticationHook('foo'));

        $service->addModule($twoFactorModule);

        $request = new TestRequest(
            [
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/_two_factor/auth/verify/totp',
            ],
            [],
            [
                '_two_factor_auth_totp_key' => '123456',
                '_two_factor_auth_redirect_to' => 'http://vpn.example/account',
            ]
        );

        $response = $service->run($request);
        $this->assertSame('foo', $session->get('_two_factor_verified'));
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://vpn.example/account', $response->getHeader('Location'));
    }

    public function testVerifyIncorrect()
    {
        $session = new TestSession();
        $tpl = new TestTpl();
        $serverClient = new ServerClient(new TestHttpClient(), 'serverClient');

        $service = new Service();
        $twoFactorModule = new TwoFactorModule(
            $serverClient,
            $session,
            $tpl
        );
        $service->addBeforeHook('auth', new NullAuthenticationHook('bar'));
        $service->addModule($twoFactorModule);
        $request = new TestRequest(
            [
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/_two_factor/auth/verify/totp',
            ],
            [],
            [
                '_two_factor_auth_totp_key' => '123456',
                '_two_factor_auth_redirect_to' => 'http://vpn.example/account',
            ]
        );

        $response = $service->run($request);
        $this->assertNull($session->get('_two_factor_verified'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"twoFactorTotp":{"_two_factor_auth_invalid":true,"_two_factor_auth_error_msg":"invalid OTP key","_two_factor_auth_redirect_to":"http:\/\/vpn.example\/account"}}', $response->getBody());
    }
}
