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

class BasicAuthenticationHook implements HookInterface
{
    /** @var array */
    private $userPass;

    /** @var string */
    private $realm;

    public function __construct(array $userPass, $realm = 'Protected Area')
    {
        $this->userPass = $userPass;
        $this->realm = $realm;
    }

    public function execute(Request $request)
    {
        $authUser = $request->getHeader('PHP_AUTH_USER', false);
        $authPass = $request->getHeader('PHP_AUTH_PW', false);
        if (is_null($authUser) || is_null($authPass)) {
            throw new HttpException(
                'missing authentication information',
                401,
                ['WWW-Authenticate' => sprintf('Basic realm="%s"', $this->realm)]
            );
        }

        if (array_key_exists($authUser, $this->userPass)) {
            // time safe string compare, using polyfill on PHP < 5.6
            if (hash_equals($this->userPass[$authUser], $authPass)) {
                return $authUser;
            }
        }

        throw new HttpException(
            'invalid authentication information',
            401,
            ['WWW-Authenticate' => sprintf('Basic realm="%s"', $this->realm)]
        );
    }
}
