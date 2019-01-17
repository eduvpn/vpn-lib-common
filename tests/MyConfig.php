<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Tests;

use LetsConnect\Common\Config;

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
