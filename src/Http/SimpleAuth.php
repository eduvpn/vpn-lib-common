<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

class SimpleAuth implements CredentialValidatorInterface
{
    /** @var array */
    private $userPass;

    public function __construct(array $userPass)
    {
        $this->userPass = $userPass;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($authUser, $authPass)
    {
        if (!array_key_exists($authUser, $this->userPass)) {
            return false;
        }

        return password_verify($authPass, $this->userPass[$authUser]);
    }
}
