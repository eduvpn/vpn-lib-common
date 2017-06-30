<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
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
        // reset all cURL options
        $this->curlReset();

        $defaultCurlOptions = [
            CURLOPT_USERPWD => sprintf('%s:%s', $this->authInfo[0], $this->authInfo[1]),
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
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

    private function curlReset()
    {
        // requires PHP >= 5.5 for curl_reset
        if (function_exists('curl_reset')) {
            curl_reset($this->curlChannel);

            return;
        }

        // reset the request method to GET, that is enough to allow for
        // multiple requests using the same cURL channel
        if (false === curl_setopt($this->curlChannel, CURLOPT_HTTPGET, true)) {
            throw new RuntimeException('unable to set cURL options');
        }
    }
}
