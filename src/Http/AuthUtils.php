<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Http\Exception\HttpException;

class AuthUtils
{
    /**
     * @return void
     */
    public static function requireUser(array $hookData, array $userList)
    {
        if (!in_array($hookData['auth'], $userList, true)) {
            throw new HttpException(
                sprintf(
                    'user "%s" is not allowed to perform this operation',
                    $hookData['auth']
                ),
                403
            );
        }
    }
}
