<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

class JsonResponse extends Response
{
    public function __construct(array $responseData, $responseCode = 200)
    {
        parent::__construct($responseCode, 'application/json');
        $this->setBody(json_encode($responseData));
    }
}
