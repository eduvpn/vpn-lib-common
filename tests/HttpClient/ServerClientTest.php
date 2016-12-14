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

namespace SURFnet\VPN\Common\HttpClient;

require_once sprintf('%s/Test/TestHttpClient.php', __DIR__);

use PHPUnit_Framework_TestCase;
use SURFnet\VPN\Common\HttpClient\Test\TestHttpClient;

class ServerClientTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $httpClient = new TestHttpClient();
        $serverClient = new ServerClient($httpClient, 'serverClient');
        $this->assertSame(true, $serverClient->get('foo'));
    }

    public function testQueryParameter()
    {
        $httpClient = new TestHttpClient();
        $serverClient = new ServerClient($httpClient, 'serverClient');
        $this->assertSame(true, $serverClient->get('foo', ['foo' => 'bar']));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\HttpClient\Exception\HttpClientException
     * @expectedExceptionMessage [400] GET "/error": errorValue
     */
    public function testError()
    {
        $httpClient = new TestHttpClient();
        $serverClient = new ServerClient($httpClient, 'serverClient');
        $serverClient->get('error');
    }

    public function testPost()
    {
        $httpClient = new TestHttpClient();
        $serverClient = new ServerClient($httpClient, 'serverClient');
        $this->assertSame(true, $serverClient->post('foo', ['foo' => 'bar']));
    }
}
