<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Tests\Http;

use LC\Common\Http\BasicAuthenticationHook;
use LC\Common\Http\Exception\HttpException;
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
        try {
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
            self::fail();
        } catch (HttpException $e) {
            self::assertSame('invalid authentication information', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testBasicAuthenticationNoAuth()
    {
        try {
            $basicAuthentication = new BasicAuthenticationHook(
                [
                    'foo' => 'bar',
                ]
            );

            $request = new TestRequest([]);

            $basicAuthentication->executeBefore($request, []);
            self::fail();
        } catch (HttpException $e) {
            self::assertSame('missing authentication information', $e->getMessage());
        }
    }
}
