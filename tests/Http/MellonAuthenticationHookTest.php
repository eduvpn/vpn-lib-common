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

    public function testUserIdAuthorization()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $auth->enableUserIdAuthorization(['https://idp.tuxed.net/saml|foo', 'https://idp.tuxed.net/saml|bar']);
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage access forbidden
     */
    public function testUserIdAuthorizationNotAuthorized()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $auth->enableUserIdAuthorization(['https://idp.tuxed.net/saml|foo', 'https://idp.tuxed.net/saml|bar']);
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_NAME_ID' => 'baz']);
        $auth->executeBefore($request, []);
    }

    public function testEntitlementAuthorization()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $auth->enableEntitlementAuthorization('MELLON_ENTITLEMENT', ['https://idp.tuxed.net/saml|urn:x:admin']);
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_ENTITLEMENT' => 'urn:x:admin', 'MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
    }

    public function testEntitlementAuthorizationMultipleEntitlements()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $auth->enableEntitlementAuthorization('MELLON_ENTITLEMENT', ['https://idp.tuxed.net/saml|urn:x:admin']);
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_ENTITLEMENT' => 'urn:x:foo;urn:x:admin', 'MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage access forbidden
     */
    public function testEntitlementAuthorizationNotAuthorized()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $auth->enableEntitlementAuthorization('MELLON_ENTITLEMENT', ['https://idp.tuxed.net/saml|urn:x:admin']);
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_ENTITLEMENT' => 'urn:x:foo', 'MELLON_NAME_ID' => 'foo']);
        $auth->executeBefore($request, []);
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage access forbidden
     */
    public function testEntitlementAuthorizationNoEntitlementAttribute()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $auth->enableEntitlementAuthorization('MELLON_ENTITLEMENT', ['https://idp.tuxed.net/saml|urn:x:admin']);
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_NAME_ID' => 'foo']);
        $auth->executeBefore($request, []);
    }

    public function testBothAuthorizationMethodsMatchesEntitlement()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $auth->enableUserIdAuthorization(['https://idp.tuxed.net/saml|abc', 'https://idp.tuxed.net/saml|def']);
        $auth->enableEntitlementAuthorization('MELLON_ENTITLEMENT', ['https://idp.tuxed.net/saml|urn:x:admin']);
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_ENTITLEMENT' => 'urn:x:admin', 'MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
    }

    public function testBothAuthorizationMethodsMatchesUserId()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $auth->enableUserIdAuthorization(['https://idp.tuxed.net/saml|foo', 'https://idp.tuxed.net/saml|def']);
        $auth->enableEntitlementAuthorization('MELLON_ENTITLEMENT', ['https://idp.tuxed.net/saml|urn:x:admin']);
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_ENTITLEMENT' => 'urn:x:admin', 'MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage access forbidden
     */
    public function testBothAuthorizationMethodsMatchesNeither()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $auth->enableUserIdAuthorization(['https://idp.tuxed.net/saml|abc', 'https://idp.tuxed.net/saml|def']);
        $auth->enableEntitlementAuthorization('MELLON_ENTITLEMENT', ['https://idp.tuxed.net/saml|urn:x:admin']);
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_ENTITLEMENT' => 'urn:x:foo', 'MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
    }
}
