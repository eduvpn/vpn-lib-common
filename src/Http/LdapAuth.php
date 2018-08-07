<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use Psr\Log\LoggerInterface;
use SURFnet\VPN\Common\Exception\LdapClientException;
use SURFnet\VPN\Common\LdapClient;

class LdapAuth implements CredentialValidatorInterface
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \SURFnet\VPN\Common\LdapClient */
    private $ldapClient;

    /** @var string */
    private $userDnTemplate;

    /** @var string */
    private $baseDn;

    /** @var string */
    private $searchFilterTemplate;

    /** @var string */
    private $entitlementAttribute;

    /** @var array<string> */
    private $adminEntitlementValueList;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param LdapClient               $ldapClient
     * @param string                   $userDnTemplate
     * @param string                   $baseDn
     * @param string                   $searchFilterTemplate
     * @param string                   $entitlementAttribute
     * @param array<string>            $adminEntitlementValueList
     */
    public function __construct(LoggerInterface $logger, LdapClient $ldapClient, $userDnTemplate, $baseDn, $searchFilterTemplate, $entitlementAttribute, array $adminEntitlementValueList)
    {
        $this->logger = $logger;
        $this->ldapClient = $ldapClient;
        $this->userDnTemplate = $userDnTemplate;
        $this->baseDn = $baseDn;
        $this->searchFilterTemplate = $searchFilterTemplate;
        $this->entitlementAttribute = $entitlementAttribute;
        $this->adminEntitlementValueList = $adminEntitlementValueList;
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
            $entitlementList = $this->getEntitlementList($authUser);
            if (false !== $entitlementList) {
                foreach ($this->adminEntitlementValueList as $adminEntitlementValue) {
                    if (in_array($adminEntitlementValue, $entitlementList, true)) {
                        return new UserInfo($authUser, ['admin']);
                    }
                }
            }

            return new UserInfo($authUser, []);
        } catch (LdapClientException $e) {
            $this->logger->warning(
                sprintf('unable to bind with DN "%s" (%s)', $userDn, $e->getMessage())
            );

            return false;
        }
    }

    /**
     * @param string $userId
     *
     * @return false|array<string>
     */
    public function getEntitlementList($userId)
    {
        $searchFilter = self::prepareSearchFilter($this->searchFilterTemplate, $userId);
        $ldapEntries = $this->ldapClient->search(
            $this->baseDn,
            $searchFilter,
            [$this->entitlementAttribute]
        );

        if (0 === $ldapEntries['count']) {
            // user does not exist
            return false;
        }

        return self::extractEntitlement($ldapEntries, $this->entitlementAttribute);
    }

    /**
     * @param string $searchFilterTemplate
     * @param string $userId
     *
     * @return string
     */
    private static function prepareSearchFilter($searchFilterTemplate, $userId)
    {
        return \str_replace('{{UID}}', LdapClient::escapeFilter($userId), $searchFilterTemplate);
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

        return \array_slice($ldapEntries[0][\strtolower($entitlementAttribute)], 1);
    }
}
