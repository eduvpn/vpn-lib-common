<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
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
        $userId = $hookData['auth']->id();
        if (!\in_array($userId, $userList, true)) {
            throw new HttpException(
                sprintf(
                    'user "%s" is not allowed to perform this operation',
                    $userId
                ),
                403
            );
        }
    }

    /**
     * @return void
     */
    public static function requireEntitlement(array $hookData, array $requiredEntitlementList)
    {
        $userId = $hookData['auth']->id();
        $userEntitlementList = $hookData['auth']->entitlementList();
        foreach ($userEntitlementList as $userEntitlement) {
            if (\in_array($userEntitlement, $requiredEntitlementList, true)) {
                return;
            }
        }

        throw new HttpException(
            sprintf(
                'user "%s" is missing the required entitlement',
                $userId
            ),
            403
        );
    }
}
