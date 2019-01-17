<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Tests\Http;

use LetsConnect\Common\Http\RedirectResponse;
use PHPUnit\Framework\TestCase;

class RedirectResponseTest extends TestCase
{
    public function testRedirect()
    {
        $response = new RedirectResponse('http://vpn.example.org/foo');
        $this->assertSame('http://vpn.example.org/foo', $response->getHeader('Location'));
        $this->assertSame(302, $response->getStatusCode());
    }
}
