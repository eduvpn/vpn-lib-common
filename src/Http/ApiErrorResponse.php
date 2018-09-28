<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Json;

class ApiErrorResponse extends Response
{
    /**
     * @param string $wrapperKey
     * @param string $errorMessage
     * @param int    $responseCode
     */
    public function __construct($wrapperKey, $errorMessage, $responseCode = 200)
    {
        parent::__construct($responseCode, 'application/json');

        $responseBody = [
            $wrapperKey => [
                'ok' => false,
                'error' => $errorMessage,
            ],
        ];

        $this->setBody(Json::encode($responseBody));
    }
}
