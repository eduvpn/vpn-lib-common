<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests;

use SURFnet\VPN\Common\Config;

class MyConfig extends Config
{
    public static function defaultConfig()
    {
        return [
            'foo' => [
                'bar' => ['baz'],
            ],
        ];
    }
}
