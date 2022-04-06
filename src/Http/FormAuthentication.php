<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Http;

use LC\Common\Http\Exception\HttpException;
use LC\Common\TplInterface;

class FormAuthentication implements ServiceModuleInterface, BeforeHookInterface
{
    /** @var \LC\Common\TplInterface */
    protected $tpl;
    /** @var CredentialValidatorInterface */
    private $credentialValidator;

    /** @var SessionInterface */
    private $session;

    /** @var StaticPermissions|null */
    private $staticPermissions = null;

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
    public function setStaticPermissions(StaticPermissions $staticPermissions)
    {
        $this->staticPermissions = $staticPermissions;
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
                $this->session->remove('_form_auth_user');

                // LDAP treats user "foo" and "foo " as the same user, but the
                // VPN portal does not, creating "ghost" users, so trim the
                // userName (ISSUE vpn-user-portal#151)
                $authUser = trim($request->requirePostParameter('userName'));
                $authPass = $request->requirePostParameter('userPass');
                $redirectTo = $request->requirePostParameter('_form_auth_redirect_to');

                // validate the URL
                if (false === filter_var($redirectTo, \FILTER_VALIDATE_URL, \FILTER_FLAG_PATH_REQUIRED)) {
                    throw new HttpException('invalid redirect_to URL', 400);
                }
                // extract the "host" part of the URL
                $redirectToHost = parse_url($redirectTo, \PHP_URL_HOST);
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
                                '_show_logout_button' => false,
                            ]
                        )
                    );

                    return $response;
                }

                $permissionList = $userInfo->getPermissionList();
                if (null !== $this->staticPermissions) {
                    // merge the StaticPermissions in the list obtained from the
                    // authentication backend (if any)
                    $permissionList = array_values(
                        array_unique(
                            array_merge(
                                $permissionList,
                                $this->staticPermissions->get(
                                    $userInfo->getUserId()
                                )
                            )
                        )
                    );
                }

                $this->session->regenerate();
                $this->session->set('_form_auth_user', $userInfo->getUserId());
                $this->session->set('_form_auth_permission_list', serialize($permissionList));

                return new RedirectResponse($redirectTo, 302);
            }
        );
    }

    /**
     * @return mixed
     */
    public function executeBefore(Request $request, array $hookData)
    {
        if (Service::isWhitelisted($request, ['POST' => ['/_form/auth/verify']])) {
            return;
        }

        if (null !== $authUser = $this->session->get('_form_auth_user')) {
            $permissionList = [];
            if (null !== $sessionValue = $this->session->get('_form_auth_permission_list')) {
                $permissionList = unserialize($sessionValue);
            }

            return new UserInfo(
                $authUser,
                $permissionList
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
                    '_show_logout_button' => false,
                ]
            )
        );

        return $response;
    }
}
