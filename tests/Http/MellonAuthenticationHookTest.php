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
use SURFnet\VPN\Common\Http\MellonAuthenticationHook;

class MellonAuthenticationHookTest extends PHPUnit_Framework_TestCase
{
    public function testNoEntityID()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
        $this->assertFalse($session->get('destroyed'));
    }

    public function testEntityID()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', true);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo', 'MELLON_IDP' => 'https://idp.example.org/saml']);
        $this->assertSame('https_idp.example.org_saml_foo', $auth->executeBefore($request, []));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage missing required field "MELLON_NAME_ID"
     */
    public function testAttributeMissing()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $request = new TestRequest([]);
        $auth->executeBefore($request, []);
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage missing required field "MELLON_IDP"
     */
    public function testEntityIDMissing()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', true);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo']);
        $auth->executeBefore($request, []);
    }

    public function testUserIdMatch()
    {
        $session = new TestSession();
        $session->set('_mellon_auth_user', 'foo');
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
        $this->assertFalse($session->get('destroyed'));
    }

    public function testUserIdMismatch()
    {
        $session = new TestSession();
        $session->set('_mellon_auth_user', 'bar');
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
        $this->assertTrue($session->get('destroyed'));
    }
}
