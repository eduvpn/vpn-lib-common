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

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Http\Exception\HttpException;

/**
 * The following mod_auth_mellon configuration flags MUST be set:.
 *
 *     MellonIdP "IDP"
 *     MellonMergeEnvVars On
 */
class MellonAuthenticationHook implements BeforeHookInterface
{
    /** @var SessionInterface */
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

        $this->verifyUserIdAuthorization($request);
        $this->verifyEntitlementAuthorization($request);

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

    private function verifyUserIdAuthorization(Request $request)
    {
        if (!is_null($this->userIdAuthorization)) {
            $userIdCheck = sprintf(
                '%s|%s',
                $request->getHeader('MELLON_IDP'),
                $request->getHeader($this->userIdAttribute)
            );

            if (!in_array($userIdCheck, $this->userIdAuthorization)) {
                throw new HttpException('access forbidden (not allowed)', 403);
            }
        }
    }

    private function verifyEntitlementAuthorization(Request $request)
    {
        if (!is_null($this->entitlementAttribute)) {
            $entityID = $request->getHeader('MELLON_IDP');
            $entitlementValue = $request->getHeader($this->entitlementAttribute, false, 'NO_ENTITLEMENT');
            $entitlementList = explode(';', $entitlementValue);
            foreach ($entitlementList as $entitlement) {
                $entitlementCheck = sprintf('%s|%s', $entityID, $entitlement);
                if (in_array($entitlementCheck, $this->entitlementAuthorization)) {
                    return;
                }
            }

            throw new HttpException('access forbidden (not entitled)', 403);
        }
    }
}
