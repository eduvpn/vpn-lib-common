<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Tests\Http;

use LetsConnect\Common\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /**
     * @return void
     */
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
