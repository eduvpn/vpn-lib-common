<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

class NullAuthenticationHook implements BeforeHookInterface
{
    /** @var string */
    private $authUser;

    /**
     * @param string $authUser
     */
    public function __construct($authUser)
    {
        $this->authUser = $authUser;
    }

    /**
     * @return UserInfo
     */
    public function executeBefore(Request $request, array $hookData)
    {
        return new UserInfo($this->authUser, []);
    }
}
