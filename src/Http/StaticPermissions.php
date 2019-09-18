<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Http;

use LC\Common\FileIO;
use LC\Common\Json;

class StaticPermissions
{
    /** @var string */
    private $permissionFile;

    /**
     * @param string $permissionFile
     */
    public function __construct($permissionFile)
    {
        $this->permissionFile = $permissionFile;
    }

    /**
     * @param string $authUser
     *
     * @return array<string>
     */
    public function get($authUser)
    {
        $groupData = Json::decode(FileIO::readFile($this->permissionFile));
        $permissionList = [];
        foreach ($groupData as $permissionId => $memberList) {
            if (!\in_array($authUser, $memberList, true)) {
                continue;
            }
            $permissionList[] = $permissionId;
        }

        return $permissionList;
    }
}
