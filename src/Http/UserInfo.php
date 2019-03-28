<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Http;

class UserInfo
{
    /** @var string */
    private $userId;

    /** @var array<string> */
    private $permissionList;

    /**
     * @param string        $userId
     * @param array<string> $permissionList
     */
    public function __construct($userId, array $permissionList)
    {
        $this->userId = $userId;
        $this->permissionList = $permissionList;
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
}
