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
    /** @var array<string,mixed> */
    private $serverData;

    /** @var array<string,string|string[]> */
    private $getData;

    /** @var array<string,string|string[]> */
    private $postData;

    /**
     * @param array<string,mixed>           $serverData
     * @param array<string,string|string[]> $getData
     * @param array<string,string|string[]> $postData
     */
    public function __construct(array $serverData, array $getData = [], array $postData = [])
    {
        $this->serverData = $serverData;
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
        $requestUri = $this->requireHeader('REQUEST_URI');

        return sprintf('%s://%s%s', $this->getScheme(), $this->getAuthority(), $requestUri);
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        $rootDir = \dirname($this->requireHeader('SCRIPT_NAME'));
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
        return $this->requireHeader('REQUEST_METHOD');
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->requireHeader('SERVER_NAME');
    }

    /**
     * @return bool
     */
    public function isBrowser()
    {
        if (null === $httpAccept = $this->optionalHeader('HTTP_ACCEPT')) {
            return false;
        }

        return false !== mb_strpos($httpAccept, 'text/html');
    }

    /**
     * @return string
     */
    public function getPathInfo()
    {
        // remove the query string
        $requestUri = $this->requireHeader('REQUEST_URI');
        if (false !== $pos = mb_strpos($requestUri, '?')) {
            $requestUri = mb_substr($requestUri, 0, $pos);
        }

        // if requestUri === scriptName
        $scriptName = $this->requireHeader('SCRIPT_NAME');
        if ($requestUri === $scriptName) {
            return '/';
        }

        // remove script_name (if it is part of request_uri
        if (0 === mb_strpos($requestUri, $scriptName)) {
            return substr($requestUri, mb_strlen($scriptName));
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
        if (null === $queryString = $this->optionalHeader('QUERY_STRING')) {
            return '';
        }

        return $queryString;
    }

    /**
     * @param string $queryKey
     *
     * @return string
     */
    public function requireQueryParameter($queryKey)
    {
        if (!\array_key_exists($queryKey, $this->getData)) {
            throw new HttpException(sprintf('missing query parameter "%s"', $queryKey), 400);
        }
        if (!\is_string($this->getData[$queryKey])) {
            throw new HttpException(sprintf('value of query parameter "%s" MUST be string', $queryKey), 400);
        }

        return $this->getData[$queryKey];
    }

    /**
     * @param string $queryKey
     *
     * @return string|null
     */
    public function optionalQueryParameter($queryKey)
    {
        if (!\array_key_exists($queryKey, $this->getData)) {
            return null;
        }

        return $this->requireQueryParameter($queryKey);
    }

    /**
     * @param string $postKey
     *
     * @return string
     */
    public function requirePostParameter($postKey)
    {
        if (!\array_key_exists($postKey, $this->postData)) {
            throw new HttpException(sprintf('missing post parameter "%s"', $postKey), 400);
        }
        if (!\is_string($this->postData[$postKey])) {
            throw new HttpException(sprintf('value of post parameter "%s" MUST be string', $postKey), 400);
        }

        return $this->postData[$postKey];
    }

    /**
     * @param string $postKey
     *
     * @return string|null
     */
    public function optionalPostParameter($postKey)
    {
        if (!\array_key_exists($postKey, $this->postData)) {
            return null;
        }

        return $this->requirePostParameter($postKey);
    }

    /**
     * @deprecated
     *
     * @return array<string,string|string[]>
     */
    public function getQueryParameters()
    {
        return $this->getData;
    }

    /**
     * @param string $headerKey
     *
     * @return string
     */
    public function requireHeader($headerKey)
    {
        if (!\array_key_exists($headerKey, $this->serverData)) {
            throw new HttpException(sprintf('missing request header "%s"', $headerKey), 400);
        }

        if (!\is_string($this->serverData[$headerKey])) {
            throw new HttpException(sprintf('value of request header "%s" MUST be string', $headerKey), 400);
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
        if (!\array_key_exists($headerKey, $this->serverData)) {
            return null;
        }

        return $this->requireHeader($headerKey);
    }
}
