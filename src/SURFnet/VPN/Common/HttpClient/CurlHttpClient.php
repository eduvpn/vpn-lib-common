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

use SURFnet\VPN\Common\HttpClient\Exception\HttpClientException;
use RuntimeException;

class CurlHttpClient implements HttpClientInterface
{
    /** @var resource */
    private $curlChannel;

    /** @var string */
    private $authUser;

    /** @var string */
    private $authPass;

    public function __construct($authUser, $authPass)
    {
        if (false === $this->curlChannel = curl_init()) {
            throw new RuntimeException('unable to initialize a cURL session');
        }
        $this->authUser = $authUser;
        $this->authPass = $authPass;
    }

    private function setOptions(array $additionalOptions)
    {
        curl_reset($this->curlChannel);
        $curlOptions = array_merge(
            [
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERPWD => sprintf('%s:%s', $this->authUser, $this->authPass),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER, [
                    'Accept: application/json',
                ],
            ],
            $additionalOptions
        );

        if (false === curl_setopt_array($this->curlChannel, $curlOptions)) {
            throw new RuntimeException('unable to set cURL options');
        }
    }

    public function __destruct()
    {
        curl_close($this->curlChannel);
    }

    /**
     * Send a HTTP GET request.
     *
     * @param string $requestUri the request URI
     *
     * @throws HttpClientException if the response is a 4xx error with the
     *                             JSON "error" field of the response body as the exception message
     */
    public function get($requestUri)
    {
        $this->setOptions(
            [
                CURLOPT_URL => $requestUri,
            ]
        );

        return $this->handleRequest();
    }

    /**
     * Send a HTTP POST request.
     *
     * @param string $requestUri the request URI
     * @param array  $postData   the HTTP POST fields to send
     *
     * @throws HttpClientException if the response is a 4xx error with the
     *                             JSON "error" field of the response body as the exception message
     */
    public function post($requestUri, array $postData)
    {
        $this->setOptions(
            [
                CURLOPT_URL => $requestUri,
                CURLOPT_POSTFIELDS => http_build_query($postData),
            ]
        );

        return $this->handleRequest();
    }

    private function handleRequest()
    {
        $curlResponse = curl_exec($this->curlChannel);

        // error in the transfer?
        if (false === $curlResponse) {
            throw new RuntimeException(
                sprintf(
                    'cURL error: %s (%s)',
                    curl_error($this->curlChannel)
                )
            );
        }

        $decodedResponseData = json_decode($curlResponse, true);

        $curlResponseCode = curl_getinfo($this->curlChannel, CURLINFO_HTTP_CODE);
        // OK?
        if ($curlResponseCode >= 200 && $curlResponseCode < 300) {
            return $decodedResponseData;
        }

        if (is_array($decodedResponseData) && array_key_exists('error', $decodedResponseData)) {
            throw new HttpClientException(
                sprintf('[%d]: %s', $curlResponseCode, $decodedResponseData['error'])
            );
        }

        throw new HttpClientException(
                sprintf('[%d]: unexpected error', $curlResponseCode)
        );
    }
}
