<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Http;

use LC\Common\Http\Exception\HttpException;

class Request
{
    /** @var array */
    private $serverData;

    /** @var array */
    private $getData;

    /** @var array */
    private $postData;

    public function __construct(array $serverData, array $getData = [], array $postData = [])
    {
        $requiredHeaders = [
            'REQUEST_METHOD',
            'SERVER_NAME',
            'SERVER_PORT',
            'REQUEST_URI',
            'SCRIPT_NAME',
        ];

        foreach ($requiredHeaders as $key) {
            if (!\array_key_exists($key, $serverData)) {
                // this indicates something wrong with the interaction between
                // the web server and PHP, these headers MUST always be available
                throw new HttpException(sprintf('missing header "%s"', $key), 500);
            }
        }
        $this->serverData = $serverData;

        // make sure getData and postData are array<string,string>
        foreach ([$getData, $postData] as $keyValueList) {
            foreach ($keyValueList as $k => $v) {
                if (!\is_string($k) || !\is_string($v)) {
                    throw new HttpException('GET/POST parameter key and value MUST be of type "string"', 400);
                }
            }
        }
        $this->getData = $getData;
        $this->postData = $postData;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        if (null === $requestScheme = $this->optionalHeader('REQUEST_SCHEME')) {
            $requestScheme = 'http';
        }

        if (!\in_array($requestScheme, ['http', 'https'], true)) {
            throw new HttpException('unsupported "REQUEST_SCHEME"', 400);
        }

        return $requestScheme;
    }

    /**
     * URI = scheme:[//authority]path[?query][#fragment]
     * authority = [userinfo@]host[:port].
     *
     * @see https://en.wikipedia.org/wiki/Uniform_Resource_Identifier#Generic_syntax
     *
     * @return string
     */
    public function getAuthority()
    {
        // we do not care about "userinfo"...
        $requestScheme = $this->getScheme();
        $serverName = $this->requireHeader('SERVER_NAME');
        $serverPort = (int) $this->requireHeader('SERVER_PORT');

        $usePort = false;
        if ('https' === $requestScheme && 443 !== $serverPort) {
            $usePort = true;
        }
        if ('http' === $requestScheme && 80 !== $serverPort) {
            $usePort = true;
        }

        if ($usePort) {
            return sprintf('%s:%d', $serverName, $serverPort);
        }

        return $serverName;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        $requestUri = $this->serverData['REQUEST_URI'];

        return sprintf('%s://%s%s', $this->getScheme(), $this->getAuthority(), $requestUri);
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        $rootDir = \dirname($this->serverData['SCRIPT_NAME']);
        if ('/' !== $rootDir) {
            return sprintf('%s/', $rootDir);
        }

        return $rootDir;
    }

    /**
     * @return string
     */
    public function getRootUri()
    {
        return sprintf('%s://%s%s', $this->getScheme(), $this->getAuthority(), $this->getRoot());
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->serverData['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->serverData['SERVER_NAME'];
    }

    /**
     * @return bool
     */
    public function isBrowser()
    {
        if (!\array_key_exists('HTTP_ACCEPT', $this->serverData)) {
            return false;
        }

        return false !== mb_strpos($this->serverData['HTTP_ACCEPT'], 'text/html');
    }

    /**
     * @return string
     */
    public function getPathInfo()
    {
        // remove the query string
        $requestUri = $this->serverData['REQUEST_URI'];
        if (false !== $pos = mb_strpos($requestUri, '?')) {
            $requestUri = mb_substr($requestUri, 0, $pos);
        }

        // if requestUri === scriptName
        if ($this->serverData['REQUEST_URI'] === $this->serverData['SCRIPT_NAME']) {
            return '/';
        }

        // remove script_name (if it is part of request_uri
        if (0 === mb_strpos($requestUri, $this->serverData['SCRIPT_NAME'])) {
            return substr($requestUri, mb_strlen($this->serverData['SCRIPT_NAME']));
        }

        // remove the root
        if ('/' !== $this->getRoot()) {
            return mb_substr($requestUri, mb_strlen($this->getRoot()) - 1);
        }

        return $requestUri;
    }

    /**
     * Return the "raw" query string.
     *
     * @return string
     */
    public function getQueryString()
    {
        if (!\array_key_exists('QUERY_STRING', $this->serverData)) {
            return '';
        }

        return $this->serverData['QUERY_STRING'];
    }

    /**
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->getData;
    }

    /**
     * @param string $key
     * @param bool   $isRequired
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getQueryParameter($key, $isRequired = true, $defaultValue = null)
    {
        return Utils::getValueFromArray($this->getData, $key, $isRequired, $defaultValue);
    }

    /**
     * @return array
     */
    public function getPostParameters()
    {
        return $this->postData;
    }

    /**
     * @param string $key
     * @param bool   $isRequired
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getPostParameter($key, $isRequired = true, $defaultValue = null)
    {
        return Utils::getValueFromArray($this->postData, $key, $isRequired, $defaultValue);
    }

    /**
     * @param string $headerKey
     *
     * @return bool
     */
    public function hasHeader($headerKey)
    {
        return \array_key_exists($headerKey, $this->serverData);
    }

    /**
     * @param string $headerKey
     *
     * @return string
     */
    public function requireHeader($headerKey)
    {
        if (!$this->hasHeader($headerKey)) {
            throw new HttpException(sprintf('missing request header "%s"', $headerKey), 400);
        }

        return $this->serverData[$headerKey];
    }

    /**
     * @param string $headerKey
     *
     * @return string|null
     */
    public function optionalHeader($headerKey)
    {
        if (!$this->hasHeader($headerKey)) {
            return null;
        }

        return $this->serverData[$headerKey];
    }
}
