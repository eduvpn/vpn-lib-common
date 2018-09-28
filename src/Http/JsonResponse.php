<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Json;

class JsonResponse extends Response
{
    /**
     * @param array $responseData
     * @param int   $responseCode
     */
    public function __construct(array $responseData, $responseCode = 200)
    {
        parent::__construct($responseCode, 'application/json');
        $this->setBody(Json::encode($responseData));
    }
}
