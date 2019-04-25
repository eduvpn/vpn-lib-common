<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\HttpClient;

use LC\Common\Json;
use LC\Common\HttpClient\Exception\HttpClientException;
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

    /**
     * @param string $requestUri
     *
     * @return array
     */
    public function get($requestUri)
    {
        return $this->exec(
            [
                CURLOPT_URL => $requestUri,
            ]
        );
    }

    /**
     * @param string $requestUri
     * @param array  $postData
     *
     * @return array
     */
    public function post($requestUri, array $postData = [])
    {
        return $this->exec(
            [
                CURLOPT_URL => $requestUri,
                CURLOPT_POSTFIELDS => http_build_query($postData),
            ]
        );
    }

    /**
     * @return array
     */
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

        $responseData = curl_exec($this->curlChannel);
        if (!\is_string($responseData)) {
            $curlError = curl_error($this->curlChannel);
            throw new RuntimeException(sprintf('failure performing the HTTP request: "%s"', $curlError));
        }

        $code = curl_getinfo($this->curlChannel, CURLINFO_HTTP_CODE);
        try {
            // TODO: throw exception if $code < 300
            return [
                $code,
                Json::decode($responseData),
            ];
        } catch (\Exception $e) {
            throw new HttpClientException(sprintf('%s: HTTP %d', $curlOptions[CURLOPT_URL], $code), $code, $e);
        }
    }

    /**
     * @return void
     */
    private function curlReset()
    {
        // requires PHP >= 5.5 for curl_reset
        if (\function_exists('curl_reset')) {
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
