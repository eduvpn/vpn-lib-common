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

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testGetServerName()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ]
        );
        $this->assertSame('vpn.example', $request->getServerName());
    }

    public function testGetRequestMethod()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ]
        );
        $this->assertSame('GET', $request->getRequestMethod());
    }

    /**
     * @expectedException SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage missing header "REQUEST_METHOD"
     */
    public function testMissingHeader()
    {
        $request = new Request([]);
    }

    public function testGetPathInfo()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/foo/bar',
                'PATH_INFO' => '/foo/bar',
            ]
        );
        $this->assertSame('/foo/bar', $request->getPathInfo());
    }

    public function testMissingPathInfo()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ]
        );
        $this->assertSame('/', $request->getPathInfo());
    }

    public function testGetQueryParameter()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/?user_id=foo',
            ],
            [
                'user_id' => 'foo',
            ]
        );
        $this->assertSame('foo', $request->getQueryParameter('user_id'));
    }

    /**
     * @expectedException SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage missing required field "user_id"
     */
    public function testGetMissingQueryParameter()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',            ]
        );
        $request->getQueryParameter('user_id');
    }

    public function testDefaultQueryParameter()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ]
        );
        $this->assertSame('bar', $request->getQueryParameter('user_id', false, 'bar'));
    }

    public function testGetPostParameter()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ],
            [],
            [
                'user_id' => 'foo',
            ]
        );
        $this->assertSame('foo', $request->getPostParameter('user_id'));
    }

    public function testGetHeader()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
                'HTTP_ACCEPT' => 'text/html',
            ]
        );
        $this->assertSame('text/html', $request->getHeader('HTTP_ACCEPT'));
    }

    public function testRequestUri()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ]
        );
        $this->assertSame('http://vpn.example/', $request->getUri());
    }

    public function testHttpsRequestUri()
    {
        $request = new Request(
            [
                'REQUEST_SCHEME' => 'https',
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 443,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ]
        );
        $this->assertSame('https://vpn.example/', $request->getUri());
    }

    public function testNonStandardPortRequestUri()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 8080,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ]
        );
        $this->assertSame('http://vpn.example:8080/', $request->getUri());
    }

    public function testGetRootSimple()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
            ]
        );
        $this->assertSame('/', $request->getRoot());
    }

    public function testGetRootSame()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/connection',
                'PATH_INFO' => '/connection',
            ]
        );
        $this->assertSame('/', $request->getRoot());
    }

    public function testGetRootPathInfo()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/admin/foo/bar',
                'PATH_INFO' => '/foo/bar',
            ]
        );
        $this->assertSame('/admin/', $request->getRoot());
    }

    public function testGetRootQueryString()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/?foo=bar',
            ]
        );
        $this->assertSame('/', $request->getRoot());
    }

    public function testGetRootPathInfoQueryString()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/admin/foo/bar?foo=bar',
                'PATH_INFO' => '/foo/bar',
            ]
        );
        $this->assertSame('/admin/', $request->getRoot());
    }
}
