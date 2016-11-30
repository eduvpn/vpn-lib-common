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

use SURFnet\VPN\Common\HttpClient\Exception\ApiException;
use SURFnet\VPN\Common\HttpClient\Exception\HttpClientException;

class BaseClient
{
    /** @var HttpClientInterface */
    private $httpClient;

    /** @var string */
    private $baseUri;

    public function __construct(HttpClientInterface $httpClient, $baseUri)
    {
        $this->httpClient = $httpClient;
        $this->baseUri = $baseUri;
    }

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

    public function post($requestPath, array $postData)
    {
        $requestUri = sprintf('%s/%s', $this->baseUri, $requestPath);

        return self::responseHandler(
            'POST',
            $requestPath,
            $this->httpClient->post($requestUri, $postData)
        );
    }

    private static function responseHandler($requestMethod, $requestPath, array $clientResponse)
    {
        list($statusCode, $responseData) = $clientResponse;

        if (is_array($responseData)) {
            // normal case
            if (array_key_exists('data', $responseData)) {
                if (array_key_exists($requestPath, $responseData['data'])) {
                    return $responseData['data'][$requestPath];
                }
            }
            // error case
            if (array_key_exists('error', $responseData)) {
                throw new ApiException(sprintf('[%d] %s', $statusCode, $responseData['error']));
            }
        }

        throw new HttpClientException(
            sprintf('[%d] malformed response for %s request to "%s"', $statusCode, $requestMethod, $requestPath)
        );
    }
}
