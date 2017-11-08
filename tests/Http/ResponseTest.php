<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit_Framework_TestCase;
use SURFnet\VPN\Common\Http\Response;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testImport()
    {
        $response = Response::import(
            [
                'statusCode' => 200,
                'responseHeaders' => ['Content-Type' => 'application/json'],
                'responseBody' => '{"a": "b"}',
            ]
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            [
                'Content-Type' => 'application/json',
            ],
            $response->getHeaders()
        );
        $this->assertSame('{"a": "b"}', $response->getBody());
    }
}
