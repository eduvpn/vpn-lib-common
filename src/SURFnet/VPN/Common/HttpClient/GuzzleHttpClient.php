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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class GuzzleHttpClient implements HttpClientInterface
{
    /** @var \GuzzleHttp\Client */
    private $httpClient;

    public function __construct(array $guzzleOptions)
    {
        // http://docs.guzzlephp.org/en/5.3/clients.html#request-options
        $defaultOptions = [
            'allow_redirects' => false,
            'timeout' => 5,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $this->httpClient = new Client(
            array_merge_recursive($defaultOptions, $guzzleOptions)
        );
    }

    public function get($requestUri, array $getData = [], array $requestHeaders = [])
    {
        try {
            $response = $this->httpClient->get(
                $requestUri,
                [
                    'headers' => $requestHeaders,
                ]
            );

            return [$response->getStatusCode(), $response->json()];
        } catch (ClientException $e) {
            // 4xx response
            return [$e->getResponse()->getStatusCode(), $e->getResponse()->json()];
        }
    }

    public function post($requestUri, array $postData, array $requestHeaders = [])
    {
        try {
            $response = $this->httpClient->post(
                $requestUri,
                [
                    'body' => [
                        $postData,
                    ],
                    'headers' => $requestHeaders,
                ]
            );

            return [$response->getStatusCode(), $response->json()];
        } catch (ClientException $e) {
            // 4xx response
            return [$e->getResponse()->getStatusCode(), $e->getResponse()->json()];
        }
    }
}
