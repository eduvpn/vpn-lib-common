<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use InvalidArgumentException;

class ApiErrorResponse extends Response
{
    /**
     * @param string $wrapperKey
     * @param string $errorMessage
     * @param int    $responseCode
     */
    public function __construct($wrapperKey, $errorMessage, $responseCode = 200)
    {
        if (!is_string($wrapperKey)) {
            throw new InvalidArgumentException('parameter must be string');
        }
        if (!is_string($errorMessage)) {
            throw new InvalidArgumentException('parameter must be string');
        }
        if (!is_int($responseCode)) {
            throw new InvalidArgumentException('parameter must be integer');
        }

        parent::__construct($responseCode, 'application/json');

        $responseBody = [
            $wrapperKey => [
                'ok' => false,
                'error' => $errorMessage,
            ],
        ];

        $this->setBody(json_encode($responseBody));
    }
}
