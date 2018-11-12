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

    /**
     * @return mixed
     */
    public function executeBefore(Request $request, array $hookData)
    {
        if ('POST' === $request->getRequestMethod()) {
            // ignore POST to verify endpoint
            if ('/_form/auth/verify' === $request->getPathInfo()) {
                return;
            }
        }

        if ($this->session->has('_form_auth_user')) {
            $entitlementList = $this->session->has('_form_auth_entitlement_list') ? $this->session->get('_form_auth_entitlement_list') : [];
            $authTime = $this->session->has('_form_auth_time') ? new DateTime($this->session->get('_form_auth_time')) : new DateTime();

            return new UserInfo(
                $this->session->get('_form_auth_user'),
                $entitlementList,
                $authTime
            );
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
