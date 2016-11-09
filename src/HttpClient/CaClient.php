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

namespace SURFnet\VPN\Common\HttpClient;

class CaClient extends BaseClient
{
    public function __construct(HttpClientInterface $httpClient, $baseUri)
    {
        parent::__construct($httpClient, $baseUri);
    }

    public function certificateList()
    {
        return $this->get('certificate_list');
    }

    public function userCertificateList($userId)
    {
        return $this->get('user_certificate_list', ['user_id' => $userId]);
    }

    public function addClientCertificate($userId, $configName)
    {
        return $this->post(
            'add_client_certificate',
            [
                'common_name' => sprintf('%s_%s', $userId, $configName),
            ]
        );
    }

    public function addServerCertificate($commonName)
    {
        return $this->post(
            'add_server_certificate',
            [
                'common_name' => $commonName,
            ]
        );
    }
}
