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

class SecurityHeadersHook implements AfterHookInterface
{
    public function executeAfter(Request $request, Response $response)
    {
        // XXX only add them if the request comes from a browser
        // CSP: https://developer.mozilla.org/en-US/docs/Security/CSP
        $response->addHeader('Content-Security-Policy', "default-src 'self'");
        // X-Frame-Options: https://developer.mozilla.org/en-US/docs/HTTP/X-Frame-Options
        $response->addHeader('X-Frame-Options', 'DENY');
        $response->addHeader('X-Content-Type-Options', 'nosniff');
        $response->addHeader('X-Xss-Protection', '1; mode=block');

        return $response;
    }
}
