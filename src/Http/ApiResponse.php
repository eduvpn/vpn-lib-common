<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use InvalidArgumentException;

class ApiResponse extends Response
{
    /**
     * @param string $wrapperKey
     * @param bool   $isOkay
     * @param mixed  $responseData
     * @param int    $responseCode
     */
    public function __construct($wrapperKey, $responseData = null, $responseCode = 200)
    {
        if (!is_string($wrapperKey)) {
            throw new InvalidArgumentException('parameter must be string');
        }
        if (!is_int($responseCode)) {
            throw new InvalidArgumentException('parameter must be integer');
        }

        $responseBody = [
            $wrapperKey => [
                'ok' => true,
            ],
        ];

        if (null !== $responseData) {
            $responseBody[$wrapperKey]['data'] = $responseData;
        }

        parent::__construct($responseCode, 'application/json');
        $this->setBody(json_encode($responseBody));
    }
}
