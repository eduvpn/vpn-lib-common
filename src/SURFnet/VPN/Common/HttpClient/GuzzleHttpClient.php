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
use SURFnet\VPN\Common\HttpClient\Exception\HttpClientException;
use GuzzleHttp\Exception\BadResponseException;
use InvalidArgumentException;
use RuntimeException;

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

    public function get($requestUri, array $requestOptions = [])
    {
        try {
            return $this->httpClient->get($requestUri, $requestOptions)->json();
        } catch (BadResponseException $e) {
            $this->handleError($e);
        }
    }

    public function post($requestUri, array $postData, array $requestOptions = [])
    {
        try {
            return $this->httpClient->post(
                $requestUri,
                array_merge_recursive(
                    $requestOptions,
                    [
                        'body' => [
                            $postData,
                        ],
                    ]
                )
            )->json();
        } catch (BadResponseException $e) {
            $this->handleError($e);
        }
    }

    public function handleError(BadResponseException $e)
    {
        try {
            $responseData = $e->getResponse()->json();
        } catch (InvalidArgumentException $e) {
            // unable to decode JSON
            throw new RuntimeException('expected JSON from HTTP endpoint');
        }

        if (!is_array($responseData) && !array_key_exists('error', $responseData)) {
            throw new RuntimeException();
        }

        throw new HttpClientException($responseData['error']);
    }
}
