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
    private $entitlementAttribute;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param LdapClient               $ldapClient
     * @param string                   $userDnTemplate
     * @param string|null              $entitlementAttribute
     */
    public function __construct(LoggerInterface $logger, LdapClient $ldapClient, $userDnTemplate, $entitlementAttribute)
    {
        $this->logger = $logger;
        $this->ldapClient = $ldapClient;
        $this->userDnTemplate = $userDnTemplate;
        $this->entitlementAttribute = $entitlementAttribute;
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

            return new UserInfo($authUser, $this->getEntitlementList($userDn), new DateTime());
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
    private function getEntitlementList($userDn)
    {
        if (null === $this->entitlementAttribute) {
            return [];
        }

        $ldapEntries = $this->ldapClient->search(
            $userDn,
            '(objectClass=*)',
            [$this->entitlementAttribute]
        );

        if (0 === $ldapEntries['count']) {
            // user does not exist
            return [];
        }

        return self::extractEntitlement($ldapEntries, $this->entitlementAttribute);
    }

    /**
     * @param array  $ldapEntries
     * @param string $entitlementAttribute
     *
     * @return array<string>
     */
    private static function extractEntitlement(array $ldapEntries, $entitlementAttribute)
    {
        if (0 === $ldapEntries[0]['count']) {
            // attribute not found for this user
            return [];
        }

        return \array_slice($ldapEntries[0][strtolower($entitlementAttribute)], 1);
    }
}
