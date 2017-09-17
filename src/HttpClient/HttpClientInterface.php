<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\HttpClient;

interface HttpClientInterface
{
    /**
     * @param string $requestUri
     */
    public function get($requestUri);

    /**
     * @param string $requestUri
     * @param array  $postData
     */
    public function post($requestUri, array $postData = []);
}
