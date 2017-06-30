<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use fkooman\SeCookie\SessionInterface;
use SURFnet\VPN\Common\TplInterface;

class FormAuthenticationHook implements BeforeHookInterface
{
    /** @var \fkooman\SeCookie\SessionInterface */
    private $session;

    /** @var \SURFnet\VPN\Common\TplInterface */
    private $tpl;

    public function __construct(SessionInterface $session, TplInterface $tpl)
    {
        $this->session = $session;
        $this->tpl = $tpl;
    }

    public function executeBefore(Request $request, array $hookData)
    {
        if ('POST' === $request->getRequestMethod()) {
            // ignore POST to verify endpoint
            if ('/_form/auth/verify' === $request->getPathInfo()) {
                return;
            }
            // ignore POST to token endpoint
            if ('/_oauth/token' === $request->getPathInfo()) {
                return;
            }
        }

        if ($this->session->has('_form_auth_user')) {
            return $this->session->get('_form_auth_user');
        }

        // any other URL, enforce authentication
        $response = new Response(200, 'text/html');
        $response->setBody(
            $this->tpl->render(
                'formAuthentication',
                [
                    '_form_auth_invalid_credentials' => false,
                    '_form_auth_redirect_to' => $request->getUri(),
                    '_form_auth_login_page' => true,
                ]
            )
        );

        return $response;
    }
}
