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

namespace SURFnet\VPN\Common\Http\Test;

use RuntimeException;
use SURFnet\VPN\Common\HttpClient\HttpClientInterface;

class TestHttpClient implements HttpClientInterface
{
    public function get($requestUri)
    {
        switch ($requestUri) {
            case 'serverClient/has_yubi_key_id?user_id=foo':
                return [200, self::wrap('has_yubi_key_id', false)];
            case 'serverClient/has_yubi_key_id?user_id=bar':
                return [200, self::wrap('has_yubi_key_id', false)];
            case 'serverClient/has_totp_secret?user_id=foo':
                return [200, self::wrap('has_totp_secret', true)];
            case 'serverClient/has_totp_secret?user_id=bar':
                return [200, self::wrap('has_totp_secret', false)];
            default:
                throw new RuntimeException(sprintf('unexpected requestUri "%s"', $requestUri));
        }
    }

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
