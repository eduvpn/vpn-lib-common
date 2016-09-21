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

    public function get($r, array $getData = [])
    {
        $requestUri = sprintf('%s/%s', $this->baseUri, $r);
        if (0 !== count($getData)) {
            $requestUri = sprintf('%s?%s', $requestUri, http_build_query($getData));
        }

        $response = $this->httpClient->get($requestUri);

        if (!is_array($response) || !array_key_exists('data', $response) || !array_key_exists($r, $response['data'])) {
            throw new HttpClientException('invalid response data format');
        }

        return $response['data'][$r];
    }

    public function post($r, array $postData)
    {
        $response = $this->httpClient->post(
            sprintf('%s/%s', $this->baseUri, $r),
            $postData
        );

        if (!is_array($response) || !array_key_exists('data', $response) || !array_key_exists($r, $response['data'])) {
            throw new HttpClientException('invalid response data format');
        }

        return $response['data'][$r];
    }
}
