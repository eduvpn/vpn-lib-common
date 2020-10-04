<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Tests\HttpClient;

use LC\Common\HttpClient\HttpClientInterface;
use LC\Common\HttpClient\HttpClientResponse;
use RuntimeException;

class TestHttpClient implements HttpClientInterface
{
    /**
     * @param string               $requestUrl
     * @param array<string,string> $queryParameters
     * @param array<string>        $requestHeaders
     *
     * @return HttpClientResponse
     */
    public function get($requestUrl, array $queryParameters, array $requestHeaders = [])
    {
        switch ($requestUrl) {
            case 'serverClient/foo':
                return new HttpClientResponse(200, [], self::wrap('foo', true));
            case 'serverClient/foo':
                if ('bar' === $queryParameters['foo']) {
                    return new HttpClientResponse(200, [], self::wrap('foo', true));
                }

                return new HttpClientResponse(400, [], self::wrapError('unexpected_request'));
            case 'serverClient/error':
                return new HttpClientResponse(400, [], json_encode(['error' => 'errorValue']));

            default:
                throw new RuntimeException(sprintf('unexpected requestUrl "%s"', $requestUrl));
        }
    }

    /**
     * @param string               $requestUrl
     * @param array<string,string> $queryParameters
     * @param array<string,string> $postData
     * @param array<string>        $requestHeaders
     *
     * @return \LC\Common\HttpClient\HttpClientResponse
     */
    public function post($requestUrl, array $queryParameters, array $postData, array $requestHeaders = [])
    {
        switch ($requestUrl) {
            case 'serverClient/foo':
                return new HttpClientResponse(200, [], self::wrap('foo', true));
            default:
                throw new RuntimeException(sprintf('unexpected requestUrl "%s"', $requestUrl));
        }
    }

    /**
     * @param mixed $key
     * @param mixed $responseData
     *
     * @return string
     */
    private static function wrap($key, $responseData)
    {
        return json_encode(
            [
                $key => [
                    'ok' => true,
                    'data' => $responseData,
                ],
            ]
        );
    }

    /**
     * @param mixed $key
     * @param mixed $errorMessage
     *
     * @return string
     */
    private static function wrapError($key, $errorMessage)
    {
        return json_encode(
            [
                $key => [
                    'ok' => false,
                    'error' => $errorMessage,
                ],
            ]
        );
    }
}
