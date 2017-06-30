<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Http\Exception\HttpException;

class CsrfProtectionHook implements BeforeHookInterface
{
    /**
     * @return bool false if the CSRF protection was not used, i.e. not a
     *              browser request, or a safe request method, true if the CSRF protection
     *              was used, and successful
     */
    public function executeBefore(Request $request, array $hookData)
    {
        if (!$request->isBrowser()) {
            // not a browser, no CSRF protected needed
            return false;
        }

        // safe methods
        if (in_array($request->getRequestMethod(), ['GET', 'HEAD', 'OPTIONS'])) {
            return false;
        }

        $uriAuthority = $request->getAuthority();
        $httpOrigin = $request->getHeader('HTTP_ORIGIN', false, null);
        if (!is_null($httpOrigin)) {
            return $this->verifyOrigin($uriAuthority, $httpOrigin);
        }

        $httpReferrer = $request->getHeader('HTTP_REFERER', false, null);
        if (!is_null($httpReferrer)) {
            return $this->verifyReferrer($uriAuthority, $httpReferrer);
        }

        throw new HttpException('CSRF protection failed, no HTTP_ORIGIN or HTTP_REFERER', 400);
    }

    public function verifyOrigin($uriAuthority, $httpOrigin)
    {
        // the HTTP_ORIGIN MUST be equal to uriAuthority
        if ($uriAuthority !== $httpOrigin) {
            throw new HttpException('CSRF protection failed: unexpected HTTP_ORIGIN', 400);
        }

        return true;
    }

    public function verifyReferrer($uriAuthority, $httpReferrer)
    {
        // the HTTP_REFERER MUST start with uriAuthority
        if (0 !== strpos($httpReferrer, sprintf('%s/', $uriAuthority))) {
            throw new HttpException('CSRF protection failed: unexpected HTTP_REFERER', 400);
        }

        return true;
    }
}
