<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Http;

interface CredentialValidatorInterface
{
    /**
     * @param string $authUser
     * @param string $authPass
     *
     * @return false|UserInfo
     */
    public function isValid($authUser, $authPass);
}
