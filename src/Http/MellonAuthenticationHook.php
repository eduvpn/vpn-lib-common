<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use DateTime;
use fkooman\SeCookie\SessionInterface;

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

    /** @var null|string */
    private $entitlementAttribute;

    /**
     * @param \fkooman\SeCookie\SessionInterface $session
     * @param string                             $userIdAttribute
     * @param bool                               $addEntityId
     * @param null|string                        $entitlementAttribute
     */
    public function __construct(SessionInterface $session, $userIdAttribute, $addEntityId, $entitlementAttribute)
    {
        $this->session = $session;
        $this->userIdAttribute = $userIdAttribute;
        $this->addEntityId = $addEntityId;
        $this->entitlementAttribute = $entitlementAttribute;
    }

    /**
     * @param Request $request
     * @param array   $hookData
     *
     * @return UserInfo
     */
    public function executeBefore(Request $request, array $hookData)
    {
        // remove the "NameID" XML construct from the identifier if
        // eduPersonTargetedID attribute was used and we receive the XML
        $userId = strip_tags($request->requireHeader($this->userIdAttribute));

        if ($this->addEntityId) {
            // add the entity ID to the user ID, this is used when we have
            // different IdPs that do not guarantee uniqueness among the used
            // user identifier attribute, e.g. NAME_ID or uid
            $userId = sprintf(
                '%s_%s',
                // strip out all "special" characters from the entityID, just
                // like mod_auth_mellon does
                preg_replace('/__*/', '_', preg_replace('/[^A-Za-z.]/', '_', $request->requireHeader('MELLON_IDP'))),
                $userId
            );
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

        // XXX would be nice if we could bind this to the actual authentication
        // time, but that seems impossible with mod_auth_mellon...
        return new UserInfo($userId, $this->getEntitlementList($request), new DateTime());
    }

    /**
     * @param Request $request
     *
     * @return array<int,string>
     */
    private function getEntitlementList(Request $request)
    {
        if (null === $this->entitlementAttribute) {
            return [];
        }
        if (!$request->hasHeader($this->entitlementAttribute)) {
            return [];
        }

        $entityID = $request->requireHeader('MELLON_IDP');
        $entitlementList = explode(';', $request->requireHeader($this->entitlementAttribute));
        /** @var array<int,string> */
        $returnEntitlementList = [];
        foreach ($entitlementList as $entitlement) {
            $returnEntitlementList[] = $entitlement;
            $returnEntitlementList[] = sprintf('%s|%s', $entityID, $entitlement);
        }

        return $returnEntitlementList;
    }
}
