<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Http;

use LC\Common\Json;

class JsonResponse extends Response
{
    /**
     * @param int $responseCode
     */
    public function __construct(array $responseData, $responseCode = 200)
    {
        parent::__construct($responseCode, 'application/json');
        $this->setBody(Json::encode($responseData));
    }
}
