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

class VpnServerApiClient extends BaseClient
{
    public function __construct(HttpClientInterface $httpClient, $vpnCaApiUri)
    {
        parent::__construct($httpClient, $vpnCaApiUri);
    }

    public function getConnections()
    {
        return $this->get('/openvpn/connections')['connections'];
    }

    public function getLog($dateTime, $ipAddress)
    {
        // XXX urlencode?!
        $requestUri = sprintf('/log?date_time=%s&ip_address=%s', urlencode($dateTime), $ipAddress);

        return $this->get($requestUri)['log'];
    }

    public function getStats()
    {
        return $this->get('/stats')['stats'];
    }

    public function getDisabledUsers()
    {
        // XXX -> ['users'] => ['disabled_users']
        return $this->get('/users/disabled')['users'];
    }

    public function getIsDisabledUser($userId)
    {
        $requestUri = sprintf('/users/is_disabled?user_id=%s', $userId);

        // XXX is_disabled
        return $this->get($requestUri)['disabled'];
    }

    public function enableUser($userId)
    {
        return $this->post('/users/enable', ['user_id' => $userId])['ok'];
    }

    public function disableUser($userId)
    {
        return $this->post('/users/disable', ['user_id' => $userId])['ok'];
    }

    public function getHasOtpSecret($userId)
    {
        $requestUri = sprintf('/users/has_otp_secret?user_id=%s', $userId);

        // XXX has_otp_secret
        return $this->get($requestUri)['otp_secret'];
    }

    public function getDisabledCommonNames()
    {
        // XXX disabled_common_names
        return $this->get('/common_names/disabled')['common_names'];
    }

    public function disableCommonName($commonName)
    {
        return $this->post('/common_names/disable', ['common_name' => $commonName])['ok'];
    }

    public function enableCommonName($commonName)
    {
        return $this->post('/common_names/enable', ['common_name' => $commonName])['ok'];
    }

    public function killCommonName($commonName)
    {
        return $this->post('/openvpn/kill', ['common_name' => $commonName])['ok'];
    }

    public function getServerPools()
    {
        // XXX pools => ???
        return $this->get('/info/server')['pools'];
    }

    public function getUserGroups($userId)
    {
        $requestUri = sprintf('/groups?user_id=%s', $userId);

        return $this->get($requestUri)['groups'];
    }

    public function hasVootToken($userId)
    {
        $requestUri = sprintf('/users/has_voot_tokens?user_id=%s', $userId);

        return $this->get($requestUri)['voot_token'];
    }

    public function setVootToken($userId, $vootToken)
    {
        $requestUri = sprintf('/users/set_voot_token', $userId);

        return $this->post($requestUri, ['user_id' => $userId, 'voot_token' => $vootToken])['ok'];
    }

    public function deleteOtpSecret($userId)
    {
        return $this->post('/users/delete_otp_secret', ['user_id' => $userId])['ok'];
    }

    public function setOtpSecret($userId, $otpSecret)
    {
        return $this->post('/users/set_otp_secret', ['user_id' => $userId, 'otp_secret' => $otpSecret])['ok'];
    }
}
