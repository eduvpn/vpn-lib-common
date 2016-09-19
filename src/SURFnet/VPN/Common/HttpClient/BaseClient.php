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

    public function get($requestUri)
    {
        $response = $this->httpClient->get(
            sprintf('%s%s', $this->baseUri, $requestUri)
        );

        return $response['data'];
    }

    public function post($requestUri, array $postData)
    {
        $response = $this->httpClient->post(
            sprintf('%s%s', $this->baseUri, $requestUri),
            $postData
        );

        return $response['data'];
    }
}
