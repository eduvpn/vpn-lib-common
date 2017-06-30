<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http\Exception;

use Exception;

class HttpException extends Exception
{
    /** @var array */
    private $responseHeaders;

    public function __construct($message, $code, array $responseHeaders = [], Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->responseHeaders = $responseHeaders;
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }
}
