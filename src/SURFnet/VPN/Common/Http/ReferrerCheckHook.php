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

class ReferrerCheckHook implements BeforeHookInterface
{
    public function executeBefore(Request $request)
    {
        if (false === strpos($request->getHeader('HTTP_ACCEPT'), 'text/html')) {
            // does not come from browser
            return false;
        }

        // these methods do not require CSRF protection as they are not
        // supposed to have side effects on the server
        $safeMethods = ['GET', 'HEAD', 'OPTIONS'];
        if (!in_array($request->getRequestMethod(), $safeMethods)) {
            $referrer = $request->getHeader('HTTP_REFERER');
            // extract the "host" part of the URL
            if (false === $referrerHost = parse_url($referrer, PHP_URL_HOST)) {
                throw new HttpException('invalid HTTP_REFERER', 400);
            }

            if ($request->getServerName() !== $referrerHost) {
                throw new HttpException('HTTP_REFERER does not match expected host', 400);
            }
        }

        return true;
    }
}
