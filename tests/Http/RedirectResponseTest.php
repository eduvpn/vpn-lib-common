<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit\Framework\TestCase;
use SURFnet\VPN\Common\Http\RedirectResponse;

class RedirectResponseTest extends TestCase
{
    public function testRedirect()
    {
        $response = new RedirectResponse('http://vpn.example.org/foo');
        $this->assertSame('http://vpn.example.org/foo', $response->getHeader('Location'));
        $this->assertSame(302, $response->getStatusCode());
    }
}
