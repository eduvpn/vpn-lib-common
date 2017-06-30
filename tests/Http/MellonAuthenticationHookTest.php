<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
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
    }

    public function testUserIdMismatch()
    {
        $session = new TestSession();
        $session->set('_mellon_auth_user', 'bar');
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, []));
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
