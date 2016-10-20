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
            'default_gateway' => false,
            'routes' => [],
            'dns' => [],
            'use_nat' => false,
            'two_factor' => false,
            'client_to_client' => false,
            'listen' => '0.0.0.0',
            'enable_log' => false,
            'enable_acl' => false,
            'acl_group_list' => [],
            'block_smb' => false,
            'forward_6' => true,
            'process_count' => 4,
            'dedicated_node' => false,
        ];
    }
}
