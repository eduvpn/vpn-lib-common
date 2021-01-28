<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\HttpClient;

interface HttpClientInterface
{
    /**
     * @param string               $requestUrl
     * @param array<string,string> $queryParameters
     * @param array<string>        $requestHeaders
     *
     * @return HttpClientResponse
     */
    public function get($requestUrl, array $queryParameters, array $requestHeaders = []);

    /**
     * @param string               $requestUrl
     * @param array<string,string> $queryParameters
     * @param array<string,string> $postData
     * @param array<string>        $requestHeaders
     *
     * @return HttpClientResponse
     */
    public function post($requestUrl, array $queryParameters, array $postData, array $requestHeaders = []);

    /**
     * @param string               $requestUrl
     * @param array<string,string> $queryParameters
     * @param string               $rawPost
     * @param array<string>        $requestHeaders
     *
     * @return HttpClientResponse
     */
    public function postRaw($requestUrl, array $queryParameters, $rawPost, array $requestHeaders = []);
}
