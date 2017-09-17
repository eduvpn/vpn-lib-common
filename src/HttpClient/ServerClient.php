<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\HttpClient;

use SURFnet\VPN\Common\HttpClient\Exception\ApiException;
use SURFnet\VPN\Common\HttpClient\Exception\HttpClientException;

class ServerClient
{
    /** @var HttpClientInterface */
    private $httpClient;

    /** @var string */
    private $baseUri;

    /**
     * @param string $baseUri
     */
    public function __construct(HttpClientInterface $httpClient, $baseUri)
    {
        $this->httpClient = $httpClient;
        $this->baseUri = $baseUri;
    }

    /**
     * @param string $requestPath
     *
     * @return array|bool
     */
    public function get($requestPath, array $getData = [])
    {
        $requestUri = sprintf('%s/%s', $this->baseUri, $requestPath);
        if (0 !== count($getData)) {
            $requestUri = sprintf('%s?%s', $requestUri, http_build_query($getData));
        }

        return self::responseHandler(
            'GET',
            $requestPath,
            $this->httpClient->get($requestUri)
        );
    }

    /**
     * @param string $requestPath
     *
     * @return array|bool
     */
    public function post($requestPath, array $postData)
    {
        $requestUri = sprintf('%s/%s', $this->baseUri, $requestPath);

        return self::responseHandler(
            'POST',
            $requestPath,
            $this->httpClient->post($requestUri, $postData)
        );
    }

    /**
     * @param string $requestMethod
     * @param string $requestPath
     *
     * @return true|array
     */
    private static function responseHandler($requestMethod, $requestPath, array $clientResponse)
    {
        list($statusCode, $responseData) = $clientResponse;
        self::validateClientResponse($requestMethod, $requestPath, $statusCode, $responseData);

        if (400 <= $statusCode) {
            // either we sent an incorrect request, or there is a server error
            throw new HttpClientException(sprintf('[%d] %s "/%s": %s', $statusCode, $requestMethod, $requestPath, $responseData['error']));
        }

        // the request was correct, and there was not a server error
        if ($responseData[$requestPath]['ok']) {
            // our request was handled correctly
            if (array_key_exists('data', $responseData[$requestPath])) {
                return $responseData[$requestPath]['data'];
            }

            return true;
        }

        // our request was not handled correctly, something went wrong...
        throw new ApiException($responseData[$requestPath]['error']);
    }

    /**
     * @param string $requestMethod
     * @param string $requestPath
     * @param int    $statusCode
     * @param array  $responseData
     *
     * @return void
     */
    private static function validateClientResponse($requestMethod, $requestPath, $statusCode, $responseData)
    {
        // responseData MUST be array
        if (!is_array($responseData)) {
            throw new HttpClientException(sprintf('[%d] %s "/%s": responseData MUST be array', $statusCode, $requestMethod, $requestPath));
        }

        if (400 <= $statusCode) {
            // if status code is 4xx or 5xx it MUST have an 'error' field
            if (!array_key_exists('error', $responseData)) {
                throw new HttpClientException(sprintf('[%d] %s "/%s": responseData MUST contain "error" field', $statusCode, $requestMethod, $requestPath));
            }

            return;
        }

        if (!array_key_exists($requestPath, $responseData)) {
            throw new HttpClientException(sprintf('[%d] %s "/%s": responseData MUST contain "%s" field', $statusCode, $requestMethod, $requestPath, $requestPath));
        }

        if (!array_key_exists('ok', $responseData[$requestPath])) {
            throw new HttpClientException(sprintf('[%d] %s "/%s": responseData MUST contain "%s/ok" field', $statusCode, $requestMethod, $requestPath, $requestPath));
        }

        if (!$responseData[$requestPath]['ok']) {
            // not OK response, MUST contain error field
            if (!array_key_exists('error', $responseData[$requestPath])) {
                throw new HttpClientException(sprintf('[%d] %s "/%s": responseData MUST contain "%s/error" field', $statusCode, $requestMethod, $requestPath, $requestPath));
            }
        }
    }
}
