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

class MellonAuthenticationHook implements BeforeHookInterface
{
    /** @var SessionInterface */
    private $session;

    /** @var string */
    private $userIdAttribute;

    /** @var bool */
    private $addEntityId;

    public function __construct(SessionInterface $session, $userIdAttribute, $addEntityId)
    {
        $this->session = $session;
        $this->userIdAttribute = $userIdAttribute;
        $this->addEntityId = $addEntityId;
    }

    public function executeBefore(Request $request, array $hookData)
    {
        if ($this->addEntityId) {
            // add the entity ID to the user ID, this is used when we have
            // different IdPs that do not guarantee uniqueness among the used
            // user identifier attribute, e.g. NAME_ID or uid
            return sprintf(
                '%s|%s',
                // strip out all "special" characters from the entityID, just
                // like mod_auth_mellon does
                preg_replace('/__*/', '_', preg_replace('/[^A-Za-z.]/', '_', $request->getHeader('MELLON_IDP'))),
                $request->getHeader($this->userIdAttribute)
            );
        }

        $userId = $request->getHeader($this->userIdAttribute);
        if ($this->session->has('_mellon_auth_user_id')) {
            if ($userId !== $this->session->get('_mellon_auth_user_id')) {
                // if we have an application session where the user_id does not
                // match the Mellon user_id we destroy the current session and
                // regenerate it below.
                $this->session->destroy();
            }
        }
        $this->session->set('_mellon_auth_user_id', $userId);

        return $userId;
    }
}
