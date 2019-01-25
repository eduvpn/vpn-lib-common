<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Http;

use DateTime;
use LetsConnect\Common\Exception\LdapClientException;
use LetsConnect\Common\LdapClient;
use Psr\Log\LoggerInterface;

class LdapAuth implements CredentialValidatorInterface
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \LetsConnect\Common\LdapClient */
    private $ldapClient;

    /** @var string */
    private $userDnTemplate;

    /** @var string|null */
    private $permissionAttribute;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param LdapClient               $ldapClient
     * @param string                   $userDnTemplate
     * @param string|null              $permissionAttribute
     */
    public function __construct(LoggerInterface $logger, LdapClient $ldapClient, $userDnTemplate, $permissionAttribute)
    {
        $this->logger = $logger;
        $this->ldapClient = $ldapClient;
        $this->userDnTemplate = $userDnTemplate;
        $this->permissionAttribute = $permissionAttribute;
    }

    /**
     * @param string $authUser
     * @param string $authPass
     *
     * @return false|UserInfo
     */
    public function isValid($authUser, $authPass)
    {
        $userDn = str_replace('{{UID}}', LdapClient::escapeDn($authUser), $this->userDnTemplate);
        try {
            $this->ldapClient->bind($userDn, $authPass);

            return new UserInfo($authUser, $this->getPermissionList($userDn), new DateTime());
        } catch (LdapClientException $e) {
            $this->logger->warning(
                sprintf('unable to bind with DN "%s" (%s)', $userDn, $e->getMessage())
            );

            return false;
        }
    }

    /**
     * @param string $userDn
     *
     * @return array<string>
     */
    private function getPermissionList($userDn)
    {
        if (null === $this->permissionAttribute) {
            return [];
        }

        $ldapEntries = $this->ldapClient->search(
            $userDn,
            '(objectClass=*)',
            [$this->permissionAttribute]
        );

        if (0 === $ldapEntries['count']) {
            // user does not exist
            return [];
        }

        return self::extractPermission($ldapEntries, $this->permissionAttribute);
    }

    /**
     * @param array  $ldapEntries
     * @param string $permissionAttribute
     *
     * @return array<string>
     */
    private static function extractPermission(array $ldapEntries, $permissionAttribute)
    {
        if (0 === $ldapEntries[0]['count']) {
            // attribute not found for this user
            return [];
        }

        return \array_slice($ldapEntries[0][strtolower($permissionAttribute)], 1);
    }
}
