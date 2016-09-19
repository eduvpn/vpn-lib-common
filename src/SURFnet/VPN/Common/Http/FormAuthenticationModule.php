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

class FormAuthenticationModule implements ServiceModuleInterface
{
    /** @var array */
    private $userPass;

    /** @var SessionInterface */
    private $session;

    /** @var SURFnet\VPN\Common\TplInterface */
    private $tpl;

    public function __construct(array $userPass, SessionInterface $session, TplInterface $tpl)
    {
        $this->userPass = $userPass;
        $this->session = $session;
        $this->tpl = $tpl;
    }

    public function init(Service $service)
    {
        $service->post(
            '/_form/auth/verify',
            function (Request $request) {
                $this->session->delete('_form_auth_user');

                $authUser = $request->getPostParameter('userName');
                $authPass = $request->getPostParameter('userPass');

                if (array_key_exists($authUser, $this->userPass)) {
                    // XXX here we MUST compare hashes!
                    // time safe string compare, using polyfill on PHP < 5.6
                    if (hash_equals($this->userPass[$authUser], $authPass)) {
                        $this->session->set('_form_auth_user', $authUser);

                        return new RedirectResponse($request->getHeader('HTTP_REFERER'));
                    }
                }

                // invalid authentication
                $response = new Response(200, 'text/html');
                $response->setBody(
                    $this->tpl->render(
                        'formAuthentication',
                        [
                            '_form_auth_redirect_to' => $request->getHeader('HTTP_REFERER'),
                        ]
                    )
                );

                return $response;
            }
        );

        $service->post(
            '/_form/auth/logout',
            function (Request $request) {
                // delete authentication information
                $this->session->delete('_form_auth_user');

                return new RedirectResponse($request->getHeader('HTTP_REFERER'));
            }
        );
    }
}
