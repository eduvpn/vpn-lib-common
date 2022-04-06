<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\HttpClient;

use LC\Common\HttpClient\Exception\HttpClientException;
use ParagonIE\ConstantTime\Base64;
use RuntimeException;

class CurlHttpClient implements HttpClientInterface
{
    /** @var array<string> */
    private $requestHeaders = [];

    /**
     * @param string|null $authUser
     * @param string|null $authPass
     */
    public function __construct($authUser = null, $authPass = null)
    {
        if (null !== $authUser) {
            $authData = $authUser;
            if (null !== $authPass) {
                $authData .= ':'.$authPass;
            }
            $this->requestHeaders[] = 'Authorization: Basic '.Base64::encode($authData);
        }
    }

    /**
     * @param string               $requestUrl
     * @param array<string,string> $queryParameters
     * @param array<string>        $requestHeaders
     *
     * @return HttpClientResponse
     */
    public function get($requestUrl, array $queryParameters, array $requestHeaders = [])
    {
        if (false === $curlChannel = curl_init()) {
            throw new RuntimeException('unable to create cURL channel');
        }

        if (0 !== \count($queryParameters)) {
            $qSep = false === strpos($requestUrl, '?') ? '?' : '&';
            $requestUrl .= $qSep.http_build_query($queryParameters);
        }

        $headerList = '';
        $curlOptions = [
            \CURLOPT_URL => $requestUrl,
            \CURLOPT_HTTPHEADER => array_merge($this->requestHeaders, $requestHeaders),
            \CURLOPT_HEADER => false,
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_FOLLOWLOCATION => false,
            \CURLOPT_CONNECTTIMEOUT => 10,
            \CURLOPT_TIMEOUT => 15,
            \CURLOPT_PROTOCOLS => \CURLPROTO_HTTP | \CURLPROTO_HTTPS,
            \CURLOPT_HEADERFUNCTION =>
            /**
             * @suppress PhanUnusedClosureParameter
             *
             * @param resource $curlChannel
             * @param string   $headerLine
             *
             * @return int
             */
            function ($curlChannel, $headerLine) use (&$headerList) {
                $headerList .= $headerLine;

                return \strlen($headerLine);
            },
        ];

        if (false === curl_setopt_array($curlChannel, $curlOptions)) {
            throw new RuntimeException('unable to set cURL options');
        }

        $responseData = curl_exec($curlChannel);
        if (!\is_string($responseData)) {
            throw new HttpClientException(sprintf('failure performing the HTTP request: "%s"', curl_error($curlChannel)));
        }

        $responseCode = (int) curl_getinfo($curlChannel, \CURLINFO_HTTP_CODE);
        curl_close($curlChannel);

        return new HttpClientResponse(
            $responseCode,
            $headerList,
            $responseData
        );
    }

    /**
     * @param string               $requestUrl
     * @param array<string,string> $queryParameters
     * @param array<string,string> $postData
     * @param array<string>        $requestHeaders
     *
     * @return HttpClientResponse
     */
    public function post($requestUrl, array $queryParameters, array $postData, array $requestHeaders = [])
    {
        return $this->postRaw(
            $requestUrl,
            $queryParameters,
            http_build_query($postData),
            $requestHeaders
        );
    }

    /**
     * @param string               $requestUrl
     * @param array<string,string> $queryParameters
     * @param string               $rawPost
     * @param array<string>        $requestHeaders
     *
     * @return HttpClientResponse
     */
    public function postRaw($requestUrl, array $queryParameters, $rawPost, array $requestHeaders = [])
    {
        // XXX do not duplicate all GET code
        if (false === $curlChannel = curl_init()) {
            throw new RuntimeException('unable to create cURL channel');
        }

        if (0 !== \count($queryParameters)) {
            $qSep = false === strpos($requestUrl, '?') ? '?' : '&';
            $requestUrl .= $qSep.http_build_query($queryParameters);
        }

        $headerList = '';
        $curlOptions = [
            \CURLOPT_URL => $requestUrl,
            \CURLOPT_HTTPHEADER => array_merge($this->requestHeaders, $requestHeaders),
            \CURLOPT_POSTFIELDS => $rawPost,
            \CURLOPT_HEADER => false,
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_FOLLOWLOCATION => false,
            \CURLOPT_CONNECTTIMEOUT => 10,
            \CURLOPT_TIMEOUT => 15,
            \CURLOPT_PROTOCOLS => \CURLPROTO_HTTP | \CURLPROTO_HTTPS,
            \CURLOPT_HEADERFUNCTION =>
            /**
             * @suppress PhanUnusedClosureParameter
             *
             * @param resource $curlChannel
             * @param string   $headerLine
             *
             * @return int
             */
            function ($curlChannel, $headerLine) use (&$headerList) {
                $headerList .= $headerLine;

                return \strlen($headerLine);
            },
        ];

        if (false === curl_setopt_array($curlChannel, $curlOptions)) {
            throw new RuntimeException('unable to set cURL options');
        }

        $responseData = curl_exec($curlChannel);
        if (!\is_string($responseData)) {
            throw new HttpClientException(sprintf('failure performing the HTTP request: "%s"', curl_error($curlChannel)));
        }

        $responseCode = (int) curl_getinfo($curlChannel, \CURLINFO_HTTP_CODE);
        curl_close($curlChannel);

        return new HttpClientResponse(
            $responseCode,
            $headerList,
            $responseData
        );
    }
}
