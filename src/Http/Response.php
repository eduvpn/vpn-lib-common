<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

class Response
{
    /** @var int */
    private $statusCode;

    /** @var string */
    private $contentType;

    /** @var array */
    private $headers = [];

    /** @var string */
    private $body = null;

    public function __construct($statusCode = 200, $contentType = 'text/plain')
    {
        $this->statusCode = $statusCode;
        $this->contentType = $contentType;
    }

    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function getHeader($key)
    {
        if (array_key_exists($key, $this->headers)) {
            return $this->headers[$key];
        }
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function send()
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header(sprintf('%s: %s', $key, $value));
        }
        if (!is_null($this->body)) {
            header(sprintf('Content-Type: %s', $this->contentType));
            echo $this->body;
        }
    }
}
