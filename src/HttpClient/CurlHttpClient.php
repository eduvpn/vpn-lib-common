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

use RuntimeException;

class CurlHttpClient implements HttpClientInterface
{
    /** @var resource */
    private $curlChannel;

    /** @var array */
    private $authInfo;

    public function __construct(array $authInfo)
    {
        if (false === $this->curlChannel = curl_init()) {
            throw new RuntimeException('unable to create cURL channel');
        }
        $this->authInfo = $authInfo;
    }

    public function __destruct()
    {
        curl_close($this->curlChannel);
    }

    public function get($requestUri)
    {
        return $this->exec(
            [
                CURLOPT_URL => $requestUri,
            ]
        );
    }

    public function post($requestUri, array $postData = [])
    {
        return $this->exec(
            [
                CURLOPT_URL => $requestUri,
                CURLOPT_POSTFIELDS => http_build_query($postData),
            ]
        );
    }

    private function exec(array $curlOptions)
    {
        $defaultCurlOptions = [
            CURLOPT_USERPWD => sprintf('%s:%s', $this->authInfo[0], $this->authInfo[1]),
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ];

        if (false === curl_setopt_array($this->curlChannel, $curlOptions + $defaultCurlOptions)) {
            throw new RuntimeException('unable to set cURL options');
        }

        if (false === $responseData = curl_exec($this->curlChannel)) {
            $curlError = curl_error($this->curlChannel);
            throw new RuntimeException(sprintf('failure performing the HTTP request: "%s"', $curlError));
        }

        return [
            curl_getinfo($this->curlChannel, CURLINFO_HTTP_CODE),
            json_decode($responseData, true),
        ];
    }
}
