<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Tests\HttpClient;

use LetsConnect\Common\HttpClient\ServerClient;
use PHPUnit\Framework\TestCase;

class ServerClientTest extends TestCase
{
    public function testGet()
    {
        $httpClient = new TestHttpClient();
        $serverClient = new ServerClient($httpClient, 'serverClient');
        $this->assertTrue($serverClient->get('foo'));
    }

    public function testQueryParameter()
    {
        $httpClient = new TestHttpClient();
        $serverClient = new ServerClient($httpClient, 'serverClient');
        $this->assertTrue($serverClient->get('foo', ['foo' => 'bar']));
    }

    /**
     * @expectedException \LetsConnect\Common\HttpClient\Exception\HttpClientException
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
        $this->assertTrue($serverClient->post('foo', ['foo' => 'bar']));
    }
}
