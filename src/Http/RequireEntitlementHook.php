<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Http\Exception\HttpException;

class RequireEntitlementHook implements BeforeHookInterface
{
    /** @var array<string> */
    private $entitlementList;

    /**
     * @param array<string> $entitlementList
     */
    public function __construct(array $entitlementList)
    {
        $this->entitlementList = $entitlementList;
    }

    /**
     * @param Request $request
     * @param array   $hookData
     *
     * @return void
     */
    public function executeBefore(Request $request, array $hookData)
    {
        $urlList = [
            '/_form/auth/verify',
            '/_form/auth/logout',
        ];

        if (in_array($request->getPathInfo(), $urlList, true)) {
            return;
        }

        if (!array_key_exists('auth', $hookData)) {
            throw new HttpException('authentication hook did not run before', 500);
        }

        $userInfo = $hookData['auth'];
        $userEntitlementList = $userInfo->entitlementList();
        foreach ($userEntitlementList as $userEntitlement) {
            if (in_array($userEntitlement, $this->entitlementList, true)) {
                return;
            }
        }

        throw new HttpException('account missing required entitlement', 403);
    }
}
