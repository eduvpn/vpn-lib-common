<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Exception\LdapClientException;
use SURFnet\VPN\Common\LdapClient;

class LdapAuth implements CredentialValidatorInterface
{
    /** @var \SURFnet\VPN\Common\LdapClient */
    private $ldapClient;

    /** @var string */
    private $userDnTemplate;

    /**
     * @param string $userDnTemplate
     */
    public function __construct(LdapClient $ldapClient, $userDnTemplate)
    {
        $this->ldapClient = $ldapClient;
        $this->userDnTemplate = $userDnTemplate;
    }

    /**
     * @param string $authUser
     * @param string $authPass
     *
     * @return bool
     */
    public function isValid($authUser, $authPass)
    {
        $userDn = str_replace('{{UID}}', $authUser, $this->userDnTemplate);
        try {
            $this->ldapClient->bind($userDn, $authPass);

            return true;
        } catch (LdapClientException $e) {
            return false;
        }
    }
}
