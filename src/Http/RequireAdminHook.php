<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Http\Exception\HttpException;

class RequireAdminHook implements BeforeHookInterface
{
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

        if (!in_array('admin', $hookData['auth']->entitlementList(), true)) {
            throw new HttpException('access forbidden', 403);
        }
    }
}
