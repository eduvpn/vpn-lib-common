<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
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
     * @return string
     */
    public function executeBefore(Request $request, array $hookData)
    {
        return $this->authUser;
    }
}
