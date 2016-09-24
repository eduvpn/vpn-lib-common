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
namespace SURFnet\VPN\Common\Http;

require_once sprintf('%s/Test/TestRequest.php', __DIR__);
require_once sprintf('%s/Test/TestSession.php', __DIR__);
require_once sprintf('%s/Test/TestTpl.php', dirname(__DIR__));

use PHPUnit_Framework_TestCase;
use SURFnet\VPN\Common\Http\Test\TestRequest;
use SURFnet\VPN\Common\Http\Test\TestSession;
use SURFnet\VPN\Common\Test\TestTpl;

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
                'PATH_INFO' => '/_form/auth/verify',
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
                'PATH_INFO' => '/_form/auth/verify',
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

        $this->assertSame('{"formAuthentication":{"_form_auth_invalid_credentials":true,"_form_auth_invalid_credentials_user":"foo","_form_auth_redirect_to":"http:\/\/vpn.example\/account","_form_auth_login_page":true}}', $response->getBody());
    }

    public function testLogout()
    {
        $session = new TestSession();
        $tpl = new TestTpl();
    }
}
