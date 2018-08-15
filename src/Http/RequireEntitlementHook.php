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
    /** @var string */
    private $requiredEntitlement;

    /**
     * @param string $requiredEntitlement
     */
    public function __construct($requiredEntitlement)
    {
        $this->requiredEntitlement = $requiredEntitlement;
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
        $entitlementList = $userInfo->entitlementList();
        if (!in_array($this->requiredEntitlement, $entitlementList, true)) {
            throw new HttpException('access forbidden', 403);
        }
    }
}
