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

    public function getClientConnections()
    {
        return $this->get('client_connections');
    }

    public function getLog(array $p)
    {
        return $this->get('log', $p);
    }

    public function getStats()
    {
        return $this->get('stats');
    }

    public function postAddClientCertificate(array $p)
    {
        return $this->post('add_client_certificate', $p);
    }

    public function postAddServerCertificate(array $p)
    {
        return $this->post('add_server_certificate', $p);
    }

    public function getClientCertificateList(array $p)
    {
        return $this->get('client_certificate_list', $p);
    }

    public function getClientCertificateInfo(array $p)
    {
        return $this->get('client_certificate_info', $p);
    }

    public function postEnableUser(array $p)
    {
        return $this->post('enable_user', $p);
    }

    public function getUserList()
    {
        return $this->get('user_list');
    }

    public function postDisableUser(array $p)
    {
        return $this->post('disable_user', $p);
    }

    public function getIsDisabledUser(array $p)
    {
        return $this->get('is_disabled_user', $p);
    }

    public function postDisableClientCertificate(array $p)
    {
        return $this->post('disable_client_certificate', $p);
    }

    public function postDeleteClientCertificate(array $p)
    {
        return $this->post('delete_client_certificate', $p);
    }

    public function postEnableClientCertificate(array $p)
    {
        return $this->post('enable_client_certificate', $p);
    }

    public function postKillClient(array $p)
    {
        return $this->post('kill_client', $p);
    }

    public function getInstanceNumber()
    {
        return $this->get('instance_number');
    }

    public function getProfileList()
    {
        return $this->get('profile_list');
    }

    public function getHasTotpSecret(array $p)
    {
        return $this->get('has_totp_secret', $p);
    }

    public function getHasVootToken(array $p)
    {
        return $this->get('has_voot_token', $p);
    }

    public function getUserGroups(array $p)
    {
        return $this->get('user_groups', $p);
    }

    public function getSystemMessages(array $p)
    {
        return $this->get('system_messages', $p);
    }

    public function postAddSystemMessage(array $p)
    {
        return $this->post('add_system_message', $p);
    }

    public function postDeleteSystemMessage(array $p)
    {
        return $this->post('delete_system_message', $p);
    }

    public function getUserMessages(array $p)
    {
        return $this->get('user_messages', $p);
    }

    public function postSetVootToken(array $p)
    {
        return $this->post('set_voot_token', $p);
    }

    public function postDeleteTotpSecret(array $p)
    {
        return $this->post('delete_totp_secret', $p);
    }

    public function postSetTotpSecret(array $p)
    {
        return $this->post('set_totp_secret', $p);
    }

    public function postVerifyTotpKey(array $p)
    {
        return $this->post('verify_totp_key', $p);
    }

    public function postConnect(array $p)
    {
        return $this->post('connect', $p);
    }

    public function postDisconnect(array $p)
    {
        return $this->post('disconnect', $p);
    }

    public function postVerifyOtp(array $p)
    {
        return $this->post('verify_otp', $p);
    }
}
