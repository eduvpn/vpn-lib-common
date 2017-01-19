<?php
/**
 *  Copyright (C) 2016 SURFnet.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
