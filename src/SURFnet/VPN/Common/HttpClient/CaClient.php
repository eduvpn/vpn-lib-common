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

class CaClient extends BaseClient implements CaClientInterface
{
    public function __construct(HttpClientInterface $httpClient, $vpnCaApiUri)
    {
        parent::__construct($httpClient, $vpnCaApiUri);
    }

    public function getCertList()
    {
        // vpn-user-portal should use the next one, getusercertlist!
        // XXX cert_list ?
        return $this->get('/certificate/');
    }

    public function getUserCertList($userId)
    {
        // XXX ['user_cert_list'] ??
        return $this->get(sprintf('/certificate?user_id=%s', $userId));
    }

    public function addConfiguration($userId, $configName)
    {
        $vpnConfigName = sprintf('%s_%s', $userId, $configName);

        // XXX certificate?!
        return $this->post('/certificate/', ['common_name' => $vpnConfigName, 'cert_type' => 'client']);
    }
}
