<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use fkooman\SeCookie\SessionInterface;
use SURFnet\VPN\Common\Http\Exception\HttpException;

/**
 * The following mod_auth_mellon configuration flags MUST be set:.
 *
 *     MellonIdP "IDP"
 *     MellonMergeEnvVars On
 */
class MellonAuthenticationHook implements BeforeHookInterface
{
    /** @var \fkooman\SeCookie\SessionInterface */
    private $session;

    /** @var string */
    private $userIdAttribute;

    /** @var bool */
    private $addEntityId;

    /** @var array|null */
    private $userIdAuthorization = null;

    /** @var string|null */
    private $entitlementAttribute = null;

    /** @var array|null */
    private $entitlementAuthorization = null;

    public function __construct(SessionInterface $session, $userIdAttribute, $addEntityId)
    {
        $this->session = $session;
        $this->userIdAttribute = $userIdAttribute;
        $this->addEntityId = $addEntityId;
    }

    public function enableUserIdAuthorization(array $userIdAuthorization)
    {
        $this->userIdAuthorization = $userIdAuthorization;
    }

    public function enableEntitlementAuthorization($entitlementAttribute, array $entitlementAuthorization)
    {
        $this->entitlementAttribute = $entitlementAttribute;
        $this->entitlementAuthorization = $entitlementAuthorization;
    }

    public function executeBefore(Request $request, array $hookData)
    {
        $userId = $request->getHeader($this->userIdAttribute);
        if ($this->addEntityId) {
            // add the entity ID to the user ID, this is used when we have
            // different IdPs that do not guarantee uniqueness among the used
            // user identifier attribute, e.g. NAME_ID or uid
            $userId = sprintf(
                '%s_%s',
                // strip out all "special" characters from the entityID, just
                // like mod_auth_mellon does
                preg_replace('/__*/', '_', preg_replace('/[^A-Za-z.]/', '_', $request->getHeader('MELLON_IDP'))),
                $userId
            );
        }

        if (!$this->verifyAuthorization($request)) {
            throw new HttpException('access forbidden', 403);
        }

        if ($this->session->has('_mellon_auth_user')) {
            if ($userId !== $this->session->get('_mellon_auth_user')) {
                // if we have an application session where the user_id does not
                // match the Mellon user_id we destroy the current session and
                // regenerate it below.
                $this->session->destroy();
            }
        }
        $this->session->set('_mellon_auth_user', $userId);

        return $userId;
    }

    private function verifyAuthorization(Request $request)
    {
        if (is_null($this->userIdAuthorization) && is_null($this->entitlementAuthorization)) {
            // authorization disabled, allow user
            return true;
        }

        // if either of these succeeds now, we allow the user
        if ($this->verifyUserIdAuthorization($request)) {
            return true;
        }

        if ($this->verifyEntitlementAuthorization($request)) {
            return true;
        }

        return false;
    }

    private function verifyUserIdAuthorization(Request $request)
    {
        if (is_null($this->userIdAuthorization)) {
            return false;
        }

        $userId = sprintf(
            '%s|%s',
            $request->getHeader('MELLON_IDP'),
            $request->getHeader($this->userIdAttribute)
        );

        return in_array($userId, $this->userIdAuthorization);
    }

    private function verifyEntitlementAuthorization(Request $request)
    {
        if (is_null($this->entitlementAuthorization)) {
            return false;
        }

        $entityID = $request->getHeader('MELLON_IDP');
        $entitlementValue = $request->getHeader($this->entitlementAttribute, false, 'NO_ENTITLEMENT');
        $entitlementList = explode(';', $entitlementValue);
        foreach ($entitlementList as $entitlement) {
            $entitlementCheck = sprintf('%s|%s', $entityID, $entitlement);
            if (in_array($entitlementCheck, $this->entitlementAuthorization)) {
                return true;
            }
        }

        return false;
    }
}
