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

use SURFnet\VPN\Common\TplInterface;

class FormAuthenticationHook implements BeforeHookInterface
{
    /** @var SessionInterface */
    private $session;

    /** @var \SURFnet\VPN\Common\TplInterface */
    private $tpl;

    public function __construct(SessionInterface $session, TplInterface $tpl)
    {
        $this->session = $session;
        $this->tpl = $tpl;
    }

    public function executeBefore(Request $request)
    {
        if ($this->session->has('_form_auth_user')) {
            return $this->session->get('_form_auth_user');
        }

        // not yet authenticated
        $response = new Response(200, 'text/html');
        $response->setBody(
            $this->tpl->render('formAuthentication', [])
        );

        return $response;
    }
}
