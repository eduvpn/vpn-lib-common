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

use PHPUnit_Framework_TestCase;

class BasicAuthenticationHookTest extends PHPUnit_Framework_TestCase
{
    public function testBasicAuthentication()
    {
        $basicAuthentication = new BasicAuthenticationHook(
            [
                'foo' => 'bar',
            ]
        );

        $request = new Request(
            [
                'REQUEST_METHOD' => 'GET',
                'SERVER_NAME' => 'vpn.example',
                'PHP_AUTH_USER' => 'foo',
                'PHP_AUTH_PW' => 'bar',
            ]
        );

        $this->assertSame('foo', $basicAuthentication->executeBefore($request));
    }

    /**
     * @expectedException SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage invalid authentication information
     */
    public function testBasicAuthenticationWrongPassword()
    {
        $basicAuthentication = new BasicAuthenticationHook(
            [
                'foo' => 'bar',
            ]
        );

        $request = new Request(
            [
                'REQUEST_METHOD' => 'GET',
                'SERVER_NAME' => 'vpn.example',
                'PHP_AUTH_USER' => 'foo',
                'PHP_AUTH_PW' => 'wrong',
            ]
        );

        $basicAuthentication->executeBefore($request);
    }

    /**
     * @expectedException SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage missing authentication information
     */
    public function testBasicAuthenticationNoAuth()
    {
        $basicAuthentication = new BasicAuthenticationHook(
            [
                'foo' => 'bar',
            ]
        );

        $request = new Request(
            [
                'REQUEST_METHOD' => 'GET',
                'SERVER_NAME' => 'vpn.example',
            ]
        );

        $basicAuthentication->executeBefore($request);
    }
}
