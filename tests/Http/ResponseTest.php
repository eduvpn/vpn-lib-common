<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit\Framework\TestCase;
use SURFnet\VPN\Common\Http\Response;

class ResponseTest extends TestCase
{
    public function testImport()
    {
        $response = Response::import(
            [
                'statusCode' => 200,
                'responseHeaders' => ['Content-Type' => 'application/json', 'X-Foo' => 'Bar'],
                'responseBody' => '{"a": "b"}',
            ]
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            [
                'Content-Type' => 'application/json',
                'X-Foo' => 'Bar',
            ],
            $response->getHeaders()
        );
        $this->assertSame('{"a": "b"}', $response->getBody());
    }
}
