<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit\Framework\TestCase;
use SURFnet\VPN\Common\Http\Exception\HttpException;
use SURFnet\VPN\Common\Http\RequireEntitlementHook;
use SURFnet\VPN\Common\Http\UserInfo;

class RequireEntitlementHookTest extends TestCase
{
    public function testAdmin()
    {
        $request = new TestRequest([]);
        $requireAdminHook = new RequireEntitlementHook('admin');
        $userInfo = new UserInfo('foo', ['admin']);
        $this->assertNull($requireAdminHook->executeBefore($request, ['auth' => $userInfo]));
    }

    public function testNoAdmin()
    {
        try {
            $r = new RequireEntitlementHook('admin');
            $r->executeBefore(new TestRequest([]), ['auth' => new UserInfo('foo', ['foo'])]);
            $this->fail();
        } catch (HttpException $e) {
            $this->assertSame('access forbidden', $e->getMessage());
        }
    }
}
