<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

class HtmlResponse extends Response
{
    public function __construct($responsePage, $responseCode = 200)
    {
        parent::__construct($responseCode, 'text/html');
        $this->setBody($responsePage);
    }
}
