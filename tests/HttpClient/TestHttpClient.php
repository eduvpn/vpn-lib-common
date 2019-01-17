<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Tests\HttpClient;

use LetsConnect\Common\HttpClient\HttpClientInterface;
use RuntimeException;

class TestHttpClient implements HttpClientInterface
{
    public function get($requestUri)
    {
        switch ($requestUri) {
            case 'serverClient/foo':
                return [200, self::wrap('foo', true)];
            case 'serverClient/foo?foo=bar':
                return [200, self::wrap('foo', true)];
            case 'serverClient/error':
                return [400, ['error' => 'errorValue']];

            default:
                throw new RuntimeException(sprintf('unexpected requestUri "%s"', $requestUri));
        }
    }

    public function post($requestUri, array $postData = [])
    {
        switch ($requestUri) {
            case 'serverClient/foo':
                return [200, self::wrap('foo', true)];
            default:
                throw new RuntimeException(sprintf('unexpected requestUri "%s"', $requestUri));
        }
    }

    private static function wrap($key, $responseData)
    {
        return [
            $key => [
                'ok' => true,
                'data' => $responseData,
            ],
        ];
    }

    private static function wrapError($key, $errorMessage)
    {
        return [
            $key => [
                'ok' => false,
                'error' => $errorMessage,
            ],
        ];
    }
}
