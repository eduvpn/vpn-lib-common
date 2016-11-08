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

namespace SURFnet\VPN\Common\Http\Test;

use SURFnet\VPN\Common\Http\Request;

class TestRequest extends Request
{
    public function __construct(array $serverData, array $getData = [], array $postData = [])
    {
        $serverData = array_merge(
            [
                'SERVER_NAME' => 'vpn.example',
                'SERVER_PORT' => 80,
                'REQUEST_SCHEME' => 'http',
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
                'PATH_INFO' => '/',
            ],
            $serverData
        );

        parent::__construct($serverData, $getData, $postData);
    }
}
