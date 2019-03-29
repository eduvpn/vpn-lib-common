<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Tests\Http;

use LetsConnect\Common\Http\BasicAuthenticationHook;
use PHPUnit\Framework\TestCase;

class BasicAuthenticationHookTest extends TestCase
{
    /**
     * @return void
     */
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

        $this->assertSame('foo', $basicAuthentication->executeBefore($request, [])->getUserId());
    }

    /**
     * @expectedException \LetsConnect\Common\Http\Exception\HttpException
     *
     * @expectedExceptionMessage invalid authentication information
     *
     * @return void
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
     * @expectedException \LetsConnect\Common\Http\Exception\HttpException
     *
     * @expectedExceptionMessage missing authentication information
     *
     * @return void
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
