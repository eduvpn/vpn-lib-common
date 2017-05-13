<?php
/**
 *  Copyright (C) 2016 SURFnet.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
            'managementIp' => 'auto',
            'blockSmb' => false,
            'reject4' => false,
            'reject6' => false,
            'vpnProtoPorts' => [
                'udp/1194',
                'udp/1195',
                'tcp/1194',
                'tcp/1195',
            ],
            'portShare' => true,
            'hideProfile' => false,
        ];
    }
}
