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

class ServerClient extends BaseClient
{
    public function __construct(HttpClientInterface $httpClient, $baseUri)
    {
        parent::__construct($httpClient, $baseUri);
    }

    public function clientConnections()
    {
        return $this->get('client_connections');
    }

    public function log($dateTime, $ipAddress)
    {
        return $this->get(
            'log',
            [
                'date_time' => $dateTime,
                'ip_address' => $ipAddress,
            ]
        );
    }

    public function stats()
    {
        return $this->get('stats');
    }

    public function addClientCertificate($userId, $displayName)
    {
        return $this->post(
            'add_client_certificate',
            [
                'user_id' => $userId,
                'display_name' => $displayName,
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

    public function listClientCertificates($userId)
    {
        return $this->get('list_client_certificates', ['user_id' => $userId]);
    }

    public function enableUser($userId)
    {
        return $this->post('enable_user', ['user_id' => $userId]);
    }

    public function userList()
    {
        return $this->get('user_list');
    }

    public function disableUser($userId)
    {
        return $this->post('disable_user', ['user_id' => $userId]);
    }

    public function isDisabledUser($userId)
    {
        return $this->get('is_disabled_user', ['user_id' => $userId]);
    }

    public function disableClientCertificate($commonName)
    {
        return $this->post('disable_client_certificate', ['common_name' => $commonName]);
    }

    public function enableClientCertificate($commonName)
    {
        return $this->post('enable_client_certificate', ['common_name' => $commonName]);
    }

    public function killClient($commonName)
    {
        return $this->post('kill_client', ['common_name' => $commonName]);
    }

    public function instanceNumber()
    {
        return $this->get('instance_number');
    }

    public function profileList()
    {
        return $this->get('profile_list');
    }

    public function hasTotpSecret($userId)
    {
        return $this->get('has_totp_secret', ['user_id' => $userId]);
    }

    public function hasVootToken($userId)
    {
        return $this->get('has_voot_token', ['user_id' => $userId]);
    }

    public function userGroups($userId)
    {
        return $this->get('user_groups', ['user_id' => $userId]);
    }

    public function motd()
    {
        return $this->get('motd');
    }

    public function setMotd($motdMessage)
    {
        return $this->post('set_motd', ['motd_message' => $motdMessage]);
    }

    public function deleteMotd()
    {
        return $this->post('delete_motd', []);
    }

    public function setVootToken($userId, $vootToken)
    {
        return $this->post(
            'set_voot_token',
            [
                'user_id' => $userId,
                'voot_token' => $vootToken,
            ]
        );
    }

    public function deleteTotpSecret($userId)
    {
        return $this->post('delete_totp_secret', ['user_id' => $userId]);
    }

    public function setTotpSecret($userId, $totpSecret, $totpKey)
    {
        return $this->post(
            'set_totp_secret',
            [
                'user_id' => $userId,
                'totp_secret' => $totpSecret,
                'totp_key' => $totpKey,
            ]
        );
    }

    public function verifyTotpKey($userId, $totpKey)
    {
        return $this->post(
            'verify_totp_key',
            [
                'user_id' => $userId,
                'totp_key' => $totpKey,
            ]
        );
    }

    public function connect($profileId, $commonName, $ip4, $ip6, $connectedAt)
    {
        return $this->post(
            'connect',
            [
                'profile_id' => $profileId,
                'common_name' => $commonName,
                'ip4' => $ip4,
                'ip6' => $ip6,
                'connected_at' => $connectedAt,
            ]
        );
    }

    public function disconnect($profileId, $commonName, $ip4, $ip6, $connectedAt, $disconnectedAt, $bytesTransferred)
    {
        return $this->post(
            'disconnect',
            [
                'profile_id' => $profileId,
                'common_name' => $commonName,
                'ip4' => $ip4,
                'ip6' => $ip6,
                'connected_at' => $connectedAt,
                'disconnected_at' => $disconnectedAt,
                'bytes_transferred' => $bytesTransferred,
            ]
        );
    }

    public function verifyOtp($commonName, $otpType, $otpKey)
    {
        return $this->post(
            'verify_otp',
            [
                'common_name' => $commonName,
                'otp_type' => $otpType,
                'otp_key' => $otpKey,
            ]
        );
    }
}
