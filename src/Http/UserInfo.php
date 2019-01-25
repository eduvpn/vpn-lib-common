<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Http;

use DateTime;

class UserInfo
{
    /** @var string */
    private $userId;

    /** @var array<string> */
    private $permissionList;

    /** @var \DateTime */
    private $authTime;

    /**
     * @param string        $userId
     * @param array<string> $permissionList
     * @param \DateTime     $authTime
     */
    public function __construct($userId, array $permissionList, DateTime $authTime)
    {
        $this->userId = $userId;
        $this->permissionList = $permissionList;
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
    public function permissionList()
    {
        return $this->permissionList;
    }

    /**
     * @return \DateTime
     */
    public function authTime()
    {
        return $this->authTime;
    }
}
