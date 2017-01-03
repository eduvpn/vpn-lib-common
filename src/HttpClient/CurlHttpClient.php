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
        $curlOptions = [
            CURLOPT_USERPWD => sprintf('%s:%s', $this->authInfo[0], $this->authInfo[1]),
            CURLOPT_URL => $requestUri,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ];

        if (false === curl_setopt_array($this->curlChannel, $curlOptions)) {
            throw new RuntimeException('unable to set cURL options');
        }

        if (false === $responseData = curl_exec($this->curlChannel)) {
            throw new RuntimeException('failure performing the HTTP request');
        }

        $responseData = json_decode($responseData, true);
        // XXX check for errors!

        return [
            curl_getinfo($this->curlChannel, CURLINFO_HTTP_CODE),
            $responseData,
        ];
    }

    public function post($requestUri, array $postData = [])
    {
        $curlOptions = [
            CURLOPT_USERPWD => sprintf('%s:%s', $this->authInfo[0], $this->authInfo[1]),
            CURLOPT_URL => $requestUri,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_POSTFIELDS => http_build_query($postData),
        ];

        if (false === curl_setopt_array($this->curlChannel, $curlOptions)) {
            throw new RuntimeException('unable to set cURL options');
        }

        if (false === $responseData = curl_exec($this->curlChannel)) {
            throw new RuntimeException('failure performing the HTTP request');
        }

        $responseData = json_decode($responseData, true);
        // XXX check for errors!

        return [
            curl_getinfo($this->curlChannel, CURLINFO_HTTP_CODE),
            $responseData,
        ];
    }
}
