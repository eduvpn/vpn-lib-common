<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Tests\Http;

use LC\Common\Http\BasicAuthenticationHook;
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
     * @return void
     */
    public function testBasicAuthenticationWrongPassword()
    {
        $this->expectException('LC\Common\Http\Exception\HttpException');
        $this->expectExceptionMessage('invalid authentication information');
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
     * @return void
     */
    public function testBasicAuthenticationNoAuth()
    {
        $this->expectException('LC\Common\Http\Exception\HttpException');
        $this->expectExceptionMessage('missing authentication information');
        $basicAuthentication = new BasicAuthenticationHook(
            [
                'foo' => 'bar',
            ]
        );

        $request = new TestRequest([]);

        $basicAuthentication->executeBefore($request, []);
    }
}
