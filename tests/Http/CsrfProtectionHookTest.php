<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Tests\Http;

use LetsConnect\Common\Http\CsrfProtectionHook;
use PHPUnit\Framework\TestCase;

class CsrfProtectionHookTest extends TestCase
{
    public function testGoodPostReferrer()
    {
        $request = new TestRequest(
            [
                'HTTP_ACCEPT' => 'text/html',
                'REQUEST_METHOD' => 'POST',
                'HTTP_REFERER' => 'http://vpn.example/foo',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $this->assertTrue($referrerCheckHook->executeBefore($request, []));
    }

    public function testGoodPostOrigin()
    {
        $request = new TestRequest(
            [
                'HTTP_ACCEPT' => 'text/html',
                'REQUEST_METHOD' => 'POST',
                'HTTP_ORIGIN' => 'http://vpn.example',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $this->assertTrue($referrerCheckHook->executeBefore($request, []));
    }

    public function testGet()
    {
        $request = new TestRequest(
            [
                'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $this->assertFalse($referrerCheckHook->executeBefore($request, []));
    }

    /**
     * @expectedException \LetsConnect\Common\Http\Exception\HttpException
     * @expectedExceptionMessage CSRF protection failed, no HTTP_ORIGIN or HTTP_REFERER
     */
    public function testCheckPostNoReferrer()
    {
        $request = new TestRequest(
            [
                'REQUEST_METHOD' => 'POST',
                'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $referrerCheckHook->executeBefore($request, []);
    }

    /**
     * @expectedException \LetsConnect\Common\Http\Exception\HttpException
     * @expectedExceptionMessage CSRF protection failed: unexpected HTTP_REFERER
     */
    public function testCheckPostWrongReferrer()
    {
        $request = new TestRequest(
            [
            'REQUEST_METHOD' => 'POST',
            'HTTP_REFERER' => 'http://www.attacker.org/foo',
            'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $referrerCheckHook->executeBefore($request, []);
    }

    /**
     * @expectedException \LetsConnect\Common\Http\Exception\HttpException
     * @expectedExceptionMessage CSRF protection failed: unexpected HTTP_ORIGIN
     */
    public function testCheckPostWrongOrigin()
    {
        $request = new TestRequest(
            [
            'REQUEST_METHOD' => 'POST',
            'HTTP_ORIGIN' => 'http://www.attacker.org',
            'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $referrerCheckHook->executeBefore($request, []);
    }

    public function testNonBrowser()
    {
        $request = new TestRequest(
            [
                'REQUEST_METHOD' => 'POST',
                'HTTP_ACCEPT' => 'application/json',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $referrerCheckHook->executeBefore($request, []);
        $this->assertTrue(true);
    }
}
