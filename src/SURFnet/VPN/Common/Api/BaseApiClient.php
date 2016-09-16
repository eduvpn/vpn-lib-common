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
namespace SURFnet\VPN\Common\Api;

use GuzzleHttp\Client;

class BaseApiClient
{
    /** @var \GuzzleHttp\Client */
    private $httpClient;

    /** @var string */
    private $baseUri;

    public function __construct(Client $httpClient, $baseUri)
    {
        $this->httpClient = $httpClient;
        $this->baseUri = $baseUri;
    }

    public function get($requestUri)
    {
        $response = $this->httpClient->get(
            sprintf('%s%s', $this->baseUri, $requestUri)
        );

        return $response->json()['data'];
    }

    public function post($requestUri, array $postData)
    {
        $response = $this->httpClient->get(
            sprintf('%s%s', $this->baseUri, $requestUri),
            [
                'body' => [
                    $postData,
                ],
            ]
        );

        return $response->json()['data'];
    }

//    protected function exec($requestMethod, $requestUri, $options = array())
//    {
//        try {
//            return $this->httpClient->$requestMethod($requestUri, $options)->json();
//        } catch (BadResponseException $e) {
//            $responseBody = $e->getResponse()->json();

//            if (array_key_exists('error_description', $responseBody)) {
//                $errorMessage = sprintf('[%d] %s (%s)', $e->getResponse()->getStatusCode(), $responseBody['error'], $responseBody['error_description']);
//            } else {
//                $errorMessage = sprintf('[%d] %s', $e->getResponse()->getStatusCode(), $responseBody['error']);
//            }

//            throw new RuntimeException($errorMessage);
//        }
//    }
}
