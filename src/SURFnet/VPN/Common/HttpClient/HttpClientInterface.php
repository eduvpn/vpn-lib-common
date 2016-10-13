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

interface HttpClientInterface
{
    /**
     * Send a HTTP GET request.
     *
     * @param string $requestUri the request URI
     *
     * @throws HttpClientException if the response is a 4xx error with the
     *                             JSON "error" field of the response body as the exception message
     */
    public function get($requestUri);

    /**
     * Send a HTTP POST request.
     *
     * @param string $requestUri the request URI
     * @param array  $postData   the HTTP POST fields to send
     *
     * @throws HttpClientException if the response is a 4xx error with the
     *                             JSON "error" field of the response body as the exception message
     */
    public function post($requestUri, array $postData);
}
