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
                'REQUEST_METHOD' => 'GET',
            ]
        );
        $this->assertSame('vpn.example', $request->getServerName());
    }

    public function testGetRequestMethod()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'REQUEST_METHOD' => 'GET',
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
                'REQUEST_METHOD' => 'GET',
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
                'REQUEST_METHOD' => 'GET',
            ]
        );
        $this->assertSame('/', $request->getPathInfo());
    }

    public function testGetQueryParameter()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'REQUEST_METHOD' => 'GET',
            ],
            [
                'user_id' => 'foo',
            ]
        );
        $this->assertSame('foo', $request->getQueryParameter('user_id'));
    }

    /**
     * @expectedException SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage missing required query parameter "user_id"
     */
    public function testGetMissingQueryParameter()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'REQUEST_METHOD' => 'GET',
            ]
        );
        $request->getQueryParameter('user_id');
    }

    public function testDefaultQueryParameter()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'REQUEST_METHOD' => 'GET',
            ]
        );
        $this->assertSame('bar', $request->getQueryParameter('user_id', false, 'bar'));
    }

    public function testGetPostParameter()
    {
        $request = new Request(
            [
                'SERVER_NAME' => 'vpn.example',
                'REQUEST_METHOD' => 'GET',
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
                'REQUEST_METHOD' => 'GET',
                'HTTP_ACCEPT' => 'text/html',
            ]
        );
        $this->assertSame('text/html', $request->getHeader('HTTP_ACCEPT'));
    }
}
