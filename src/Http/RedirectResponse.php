<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

class RedirectResponse extends Response
{
    /**
     * @param string $redirectUri
     * @param int    $statusCode
     */
    public function __construct($redirectUri, $statusCode = 302)
    {
        parent::__construct($statusCode);
        $this->addHeader('Location', $redirectUri);
    }
}
