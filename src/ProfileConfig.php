<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common;

class ProfileConfig extends Config
{
    public function __construct(array $configData)
    {
        parent::__construct($configData);
    }

    public static function defaultConfig()
    {
        return [
            'defaultGateway' => false,
            'routes' => [],
            'dns' => [],
            'useNat' => false,
            'twoFactor' => false,
            'clientToClient' => false,
            'listen' => '::',
            'enableLog' => false,
            'enableAcl' => false,
            'aclGroupList' => [],
            'managementIp' => '127.0.0.1',
            'blockSmb' => false,
            'reject4' => false,
            'reject6' => false,
            'vpnProtoPorts' => [
                'udp/1194',
                'tcp/1194',
            ],
            'hideProfile' => false,
            'tlsCrypt' => false,
        ];
    }
}
