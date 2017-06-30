<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit_Framework_TestCase;
use SURFnet\VPN\Common\Http\BasicAuthenticationHook;

class BasicAuthenticationHookTest extends PHPUnit_Framework_TestCase
{
    public function testBasicAuthentication()
    {
        $basicAuthentication = new BasicAuthenticationHook(
            [
                'foo' => 'bar',
            ]
        );

        $request = new TestRequest(
            [
                'PHP_AUTH_USER' => 'foo',
                'PHP_AUTH_PW' => 'bar',
            ]
        );

        $this->assertSame('foo', $basicAuthentication->executeBefore($request, []));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage invalid authentication information
     */
    public function testBasicAuthenticationWrongPassword()
    {
        $basicAuthentication = new BasicAuthenticationHook(
            [
                'foo' => 'bar',
            ]
        );

        $request = new TestRequest(
            [
                'PHP_AUTH_USER' => 'foo',
                'PHP_AUTH_PW' => 'baz',
            ]
        );

        $basicAuthentication->executeBefore($request, []);
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage missing authentication information
     */
    public function testBasicAuthenticationNoAuth()
    {
        $basicAuthentication = new BasicAuthenticationHook(
            [
                'foo' => 'bar',
            ]
        );

        $request = new TestRequest([]);

        $basicAuthentication->executeBefore($request, []);
    }
}
