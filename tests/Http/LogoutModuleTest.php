<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit\Framework\TestCase;
use SURFnet\VPN\Common\Http\LogoutModule;
use SURFnet\VPN\Common\Http\Service;

class LogoutModuleTest extends TestCase
{
    public function testVerifyLogout()
    {
        $session = new TestSession();
        $service = new Service();
        $logoutModule = new LogoutModule($session, false);
        $service->addModule($logoutModule);
        $request = new TestRequest(
            [
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/_logout',
                'HTTP_REFERER' => 'http://example.org/foo',
            ],
            [],
            []
        );
        $response = $service->run($request);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://example.org/foo', $response->getHeader('Location'));
    }

    public function testVerifyMellonLogout()
    {
        $session = new TestSession();
        $service = new Service();
        $logoutModule = new LogoutModule($session, true);
        $service->addModule($logoutModule);
        $request = new TestRequest(
            [
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/_logout',
                'HTTP_REFERER' => 'http://example.org/foo',
            ],
            [],
            []
        );
        $response = $service->run($request);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://vpn.example/saml/logout?ReturnTo=http%3A%2F%2Fexample.org%2Ffoo', $response->getHeader('Location'));
    }
}
