<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Http;

use LC\Common\Exception\LdapClientException;
use LC\Common\LdapClient;
use Psr\Log\LoggerInterface;

class ADLdapAuth implements CredentialValidatorInterface
{
    const LDAP_MATCHING_RULE_IN_CHAIN_OID = '1.2.840.113556.1.4.1941';

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \LC\Common\LdapClient */
    private $ldapClient;

    /** @var string */
    private $bindDnTemplate;

    /** @var string|null */
    private $baseDn;

    /** @var array */
    private $permissionMemberships;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param LdapClient               $ldapClient
     * @param string                   $bindDnTemplate
     * @param string|null              $baseDn
     * @param array                    $permissionMemberships
     */
    public function __construct(LoggerInterface $logger, LdapClient $ldapClient, $bindDnTemplate, $baseDn, array $permissionMemberships)
    {
        $this->logger = $logger;
        $this->ldapClient = $ldapClient;
        $this->bindDnTemplate = $bindDnTemplate;
        $this->baseDn = $baseDn;
        $this->permissionMemberships = $permissionMemberships;
    }

    /**
     * @param string $authUser
     * @param string $authPass
     *
     * @return false|UserInfo
     */
    public function isValid($authUser, $authPass)
    {
        $bindDn = str_replace('{{UID}}', LdapClient::escapeDn($authUser), $this->bindDnTemplate);
        try {
            $this->ldapClient->bind($bindDn, $authPass);

            $baseDn = $bindDn;
            if (null !== $this->baseDn) {
                $baseDn = $this->baseDn;
            }

            $permissions = $this->getPermissionList($baseDn, $bindDn);

            if (0 === \count($permissions)) {
                throw new LdapClientException('no required membership');
            }

            return new UserInfo($authUser, $permissions);
        } catch (LdapClientException $e) {
            $this->logger->warning(
                sprintf('unable to bind with DN "%s" (%s)', $bindDn, $e->getMessage())
            );

            return false;
        }
    }

    /**
     * @param string $baseDn
     * @param string $bindDn
     *
     * @return array<string>
     */
    private function getPermissionList($baseDn, $bindDn)
    {
        $permissions = [];
        foreach ($this->permissionMemberships as $group => $perm) {
            $ldapEntries = $this->ldapClient->search(
                $baseDn,
                sprintf(
                    '(&(userPrincipalName=%s)(memberOf:%s:=%s))',
                    $bindDn,
                    self::LDAP_MATCHING_RULE_IN_CHAIN_OID,
                    LdapClient::escapeDn($group)
                ),
                []
            );

            if (0 < $ldapEntries['count']) {
                $permissions[] = $perm;
            }
        }

        return $permissions;
    }
}
