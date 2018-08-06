<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

class UserInfo
{
    /** @var string */
    private $userId;

    /** @var array<string> */
    private $entitlementList;

    /**
     * @param string        $userId
     * @param array<string> $entitlementList
     */
    public function __construct($userId, $entitlementList)
    {
        $this->userId = $userId;
        $this->entitlementList = $entitlementList;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->userId;
    }

    /**
     * @return array<string>
     */
    public function entitlementList()
    {
        return $this->entitlementList;
    }
}
