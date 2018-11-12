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
use SURFnet\VPN\Common\Http\Exception\HttpException;
use SURFnet\VPN\Common\TplInterface;

class FormAuthenticationModule implements ServiceModuleInterface
{
    /** @var CredentialValidatorInterface */
    private $credentialValidator;

    /** @var \fkooman\SeCookie\SessionInterface */
    private $session;

    /** @var \SURFnet\VPN\Common\TplInterface */
    private $tpl;

    public function __construct(
        CredentialValidatorInterface $credentialValidator,
        SessionInterface $session,
        TplInterface $tpl
    ) {
        $this->credentialValidator = $credentialValidator;
        $this->session = $session;
        $this->tpl = $tpl;
    }

    /**
     * @return void
     */
    public function init(Service $service)
    {
        $service->post(
            '/_form/auth/verify',
            /**
             * @return Response
             */
            function (Request $request) {
                $this->session->delete('_form_auth_user');

                $authUser = $request->getPostParameter('userName');
                $authPass = $request->getPostParameter('userPass');
                $redirectTo = $request->getPostParameter('_form_auth_redirect_to');

                // validate the URL
                if (false === filter_var($redirectTo, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED | FILTER_FLAG_PATH_REQUIRED)) {
                    throw new HttpException('invalid redirect_to URL', 400);
                }
                // extract the "host" part of the URL
                $redirectToHost = parse_url($redirectTo, PHP_URL_HOST);
                if (!\is_string($redirectToHost)) {
                    throw new HttpException('invalid redirect_to URL, unable to extract host', 400);
                }
                if ($request->getServerName() !== $redirectToHost) {
                    throw new HttpException('redirect_to does not match expected host', 400);
                }

                if (false === $userInfo = $this->credentialValidator->isValid($authUser, $authPass)) {
                    // invalid authentication
                    $response = new Response(200, 'text/html');
                    $response->setBody(
                        $this->tpl->render(
                            'formAuthentication',
                            [
                                '_form_auth_invalid_credentials' => true,
                                '_form_auth_invalid_credentials_user' => $authUser,
                                '_form_auth_redirect_to' => $redirectTo,
                                '_form_auth_login_page' => true,
                            ]
                        )
                    );

                    return $response;
                }

                $this->session->regenerate(true);
                $this->session->set('_form_auth_user', $userInfo->id());
                $this->session->set('_form_auth_entitlement_list', $userInfo->entitlementList());
                $this->session->set('_form_auth_time', $userInfo->authTime()->format(DateTime::ATOM));

                return new RedirectResponse($redirectTo, 302);
            }
        );

        $service->post(
            '/_form/auth/logout',
            /**
             * @return Response
             */
            function (Request $request) {
                // delete authentication information
                $this->session->delete('_form_auth_user');
                $this->session->delete('_form_auth_entitlement_list');
                $this->session->delete('_form_auth_time');
                $this->session->delete('_two_factor_verified');
                $this->session->delete('_cached_groups_user_id');
                $this->session->delete('_cached_groups');
                $this->session->delete('_last_authenticated_at_ping_sent');
                $this->session->regenerate(true);

                return new RedirectResponse($request->requireHeader('HTTP_REFERER'));
            }
        );
    }
}
