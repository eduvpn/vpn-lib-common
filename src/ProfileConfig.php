<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common;

class ProfileConfig
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function profileNumber()
    {
        return $this->config->requireInt('profileNumber');
    }

    /**
     * @return string
     */
    public function hostName()
    {
        return $this->config->requireString('hostName');
    }

    /**
     * @return string
     */
    public function range()
    {
        return $this->config->requireString('range');
    }

    /**
     * @return string
     */
    public function range6()
    {
        return $this->config->requireString('range6');
    }

    /**
     * @return string
     */
    public function displayName()
    {
        return $this->config->requireString('displayName');
    }

    /**
     * @return bool
     */
    public function defaultGateway()
    {
        return $this->config->requireBool('defaultGateway', false);
    }

    /**
     * @return array
     */
    public function routes()
    {
        return $this->config->requireArray('routes', []);
    }

    /**
     * @return array
     */
    public function dns()
    {
        return $this->config->requireArray('dns', []);
    }

    /**
     * @deprecated
     *
     * @return array
     */
    public function dnsSuffix()
    {
        return $this->config->requireArray('dnsSuffix', []);
    }

    /**
     * @return bool
     */
    public function clientToClient()
    {
        return $this->config->requireBool('clientToClient', false);
    }

    /**
     * @return string
     */
    public function listen()
    {
        return $this->config->requireString('listen', '::');
    }

    /**
     * @return bool
     */
    public function enableLog()
    {
        return $this->config->requireBool('enableLog', false);
    }

    /**
     * @return bool
     */
    public function enableAcl()
    {
        return $this->config->requireBool('enableAcl', false);
    }

    /**
     * @return array
     */
    public function aclPermissionList()
    {
        return $this->config->requireArray('aclPermissionList', []);
    }

    /**
     * @return string
     */
    public function managementIp()
    {
        return $this->config->requireString('managementIp', '127.0.0.1');
    }

    /**
     * @return array
     */
    public function vpnProtoPorts()
    {
        return $this->config->requireArray('vpnProtoPorts', ['udp/1194', 'tcp/1194']);
    }

    /**
     * @return array
     */
    public function exposedVpnProtoPorts()
    {
        return $this->config->requireArray('exposedVpnProtoPorts', []);
    }

    /**
     * @return bool
     */
    public function hideProfile()
    {
        return $this->config->requireBool('hideProfile', false);
    }

    /**
     * @return string
     */
    public function tlsProtection()
    {
        return $this->config->requireString('tlsProtection', 'tls-crypt');
    }

    /**
     * @return bool
     */
    public function blockLan()
    {
        return $this->config->requireBool('blockLan', false);
    }

    /**
     * @return bool
     */
    public function tlsOneThree()
    {
        return $this->config->requireBool('tlsOneThree', false);
    }

    /**
     * @return string|null
     */
    public function dnsDomain()
    {
        return $this->config->optionalString('dnsDomain');
    }

    /**
     * @return array
     */
    public function dnsDomainSearch()
    {
        return $this->config->requireArray('dnsDomainSearch', []);
    }

    /**
     * @deprecated
     *
     * @return array
     */
    public function toArray()
    {
        return $this->config->toArray();
    }
}
