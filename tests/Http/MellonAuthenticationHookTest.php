<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit\Framework\TestCase;
use SURFnet\VPN\Common\Http\Exception\HttpException;
use SURFnet\VPN\Common\Http\MellonAuthenticationHook;

class MellonAuthenticationHookTest extends TestCase
{
    public function testNoEntityID()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false, null);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, [])->id());
    }

    public function testEntityID()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', true, null);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo', 'MELLON_IDP' => 'https://idp.example.org/saml']);
        $this->assertSame('https_idp.example.org_saml_foo', $auth->executeBefore($request, [])->id());
    }

    public function testAttributeMissing()
    {
        try {
            $session = new TestSession();
            $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false, null);
            $request = new TestRequest([]);
            $auth->executeBefore($request, []);
            $this->fail();
        } catch (HttpException $e) {
            $this->assertSame('missing request header "MELLON_NAME_ID"', $e->getMessage());
        }
    }

    public function testEntityIDMissing()
    {
        try {
            $session = new TestSession();
            $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', true, null);
            $request = new TestRequest(['MELLON_NAME_ID' => 'foo']);
            $auth->executeBefore($request, []);
        } catch (HttpException $e) {
            $this->assertSame('missing request header "MELLON_IDP"', $e->getMessage());
        }
    }

    public function testUserIdMatch()
    {
        $session = new TestSession();
        $session->set('_mellon_auth_user', 'foo');
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false, null);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, [])->id());
    }

    public function testUserIdMismatch()
    {
        $session = new TestSession();
        $session->set('_mellon_auth_user', 'bar');
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false, null);
        $request = new TestRequest(['MELLON_NAME_ID' => 'foo']);
        $this->assertSame('foo', $auth->executeBefore($request, [])->id());
    }

    public function testEntitlementAuthorization()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false, 'MELLON_ENTITLEMENT');
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_ENTITLEMENT' => 'urn:x:admin', 'MELLON_NAME_ID' => 'foo']);
        $userInfo = $auth->executeBefore($request, []);
        $this->assertSame('foo', $userInfo->id());
        $this->assertSame(['urn:x:admin', 'https://idp.tuxed.net/saml|urn:x:admin'], $userInfo->entitlementList());
    }

    public function testEntitlementAuthorizationMultipleEntitlements()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false, 'MELLON_ENTITLEMENT');
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_ENTITLEMENT' => 'urn:x:foo;urn:x:admin', 'MELLON_NAME_ID' => 'foo']);
        $userInfo = $auth->executeBefore($request, []);
        $this->assertSame('foo', $userInfo->id());
        $this->assertSame(['urn:x:foo', 'https://idp.tuxed.net/saml|urn:x:foo', 'urn:x:admin', 'https://idp.tuxed.net/saml|urn:x:admin'], $userInfo->entitlementList());
    }

    public function testEntitlementAuthorizationNoEntitlementAttribute()
    {
        $session = new TestSession();
        $auth = new MellonAuthenticationHook($session, 'MELLON_NAME_ID', false, 'MELLON_ENTITLEMENT');
        $request = new TestRequest(['MELLON_IDP' => 'https://idp.tuxed.net/saml', 'MELLON_NAME_ID' => 'foo']);

        $userInfo = $auth->executeBefore($request, []);
        $this->assertSame('foo', $userInfo->id());
        $this->assertSame([], $userInfo->entitlementList());
    }
}
