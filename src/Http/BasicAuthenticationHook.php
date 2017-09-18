<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Http\Exception\HttpException;

class BasicAuthenticationHook implements BeforeHookInterface
{
    /** @var array */
    private $userPass;

    /** @var string */
    private $realm;

    /**
     * @param string $realm
     */
    public function __construct(array $userPass, $realm = 'Protected Area')
    {
        $this->userPass = $userPass;
        $this->realm = $realm;
    }

    /**
     * @return string
     */
    public function executeBefore(Request $request, array $hookData)
    {
        $authUser = $request->getHeader('PHP_AUTH_USER', false);
        $authPass = $request->getHeader('PHP_AUTH_PW', false);
        if (null === $authUser || null === $authPass) {
            throw new HttpException(
                'missing authentication information',
                401,
                ['WWW-Authenticate' => sprintf('Basic realm="%s"', $this->realm)]
            );
        }

        if (array_key_exists($authUser, $this->userPass)) {
            if (hash_equals($authPass, $this->userPass[$authUser])) {
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
