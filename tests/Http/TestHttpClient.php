<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Tests\Http;

use LC\Common\HttpClient\HttpClientInterface;
use RuntimeException;

class TestHttpClient implements HttpClientInterface
{
    /**
     * @param string $requestUri
     *
     * @return array{0: int, 1: string}
     */
    public function get($requestUri)
    {
        switch ($requestUri) {
            case 'serverClient/has_totp_secret?user_id=foo':
                return [200, self::wrap('has_totp_secret', true)];
            case 'serverClient/has_totp_secret?user_id=bar':
                return [200, self::wrap('has_totp_secret', false)];
            default:
                throw new RuntimeException(sprintf('unexpected requestUri "%s"', $requestUri));
        }
    }

    /**
     * @param string $requestUri
     *
     * @return array{0: int, 1: string}
     */
    public function post($requestUri, array $postData = [])
    {
        switch ($requestUri) {
            case 'serverClient/verify_totp_key':
                if ('foo' === $postData['user_id']) {
                    return [200, self::wrap('verify_totp_key', true)];
                }

                return [200, self::wrapError('verify_totp_key', 'invalid OTP key')];
            default:
                throw new RuntimeException(sprintf('unexpected requestUri "%s"', $requestUri));
        }
    }

    /**
     * @param string $key
     * @param mixed  $responseData
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
     * @param string $key
     * @param mixed  $errorMessage
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
