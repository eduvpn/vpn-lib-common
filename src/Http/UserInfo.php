<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use DateTime;

class UserInfo
{
    /** @var string */
    private $userId;

    /** @var array<string> */
    private $entitlementList;

    /** @var \DateTime */
    private $authTime;

    /**
     * @param string        $userId
     * @param array<string> $entitlementList
     * @param \DateTime     $authTime
     */
    public function __construct($userId, $entitlementList, DateTime $authTime)
    {
        $this->userId = $userId;
        $this->entitlementList = $entitlementList;
        $this->authTime = $authTime;
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

    /**
     * @return \DateTime
     */
    public function authTime()
    {
        return $this->authTime;
    }
}
