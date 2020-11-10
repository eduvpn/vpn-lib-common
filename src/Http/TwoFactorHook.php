<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Http;

use LC\Common\Http\Exception\HttpException;
use LC\Common\HttpClient\ServerClient;
use LC\Common\TplInterface;

class TwoFactorHook implements BeforeHookInterface
{
    /** @var SessionInterface */
    private $session;

    /** @var \LC\Common\TplInterface */
    private $tpl;

    /** @var \LC\Common\HttpClient\ServerClient */
    private $serverClient;

    /** @var bool */
    private $requireTwoFactor;

    /**
     * @param bool $requireTwoFactor
     */
    public function __construct(SessionInterface $session, TplInterface $tpl, ServerClient $serverClient, $requireTwoFactor)
    {
        $this->session = $session;
        $this->tpl = $tpl;
        $this->serverClient = $serverClient;
        $this->requireTwoFactor = $requireTwoFactor;
    }

    /**
     * @return bool|Response
     */
    public function executeBefore(Request $request, array $hookData)
    {
        if (!\array_key_exists('auth', $hookData)) {
            throw new HttpException('authentication hook did not run before', 500);
        }

        // some URIs are allowed as they are used for either logging in, or
        // verifying the OTP key
        $whiteList = [
            'POST' => [
                '/two_factor_enroll',
                '/_saml/acs',
                '/_form/auth/verify',
                '/_form/auth/logout',
                '/_logout',
                '/_two_factor/auth/verify/totp',
            ],
            'GET' => [
                '/_saml/login',
                '/_saml/logout',
                '/_saml/metadata',
                '/two_factor_enroll',
                '/qr',
                '/documentation',
            ],
        ];
        if (Service::isWhitelisted($request, $whiteList)) {
            return false;
        }

        /** @var UserInfo */
        $userInfo = $hookData['auth'];
        if (null !== $twoFactorVerified = $this->session->get('_two_factor_verified')) {
            if ($userInfo->getUserId() !== $twoFactorVerified) {
                throw new HttpException('two-factor code not bound to authenticated user', 400);
            }

            return true;
        }

        // check if user is enrolled
        $hasTotpSecret = $this->serverClient->getRequireBool('has_totp_secret', ['user_id' => $userInfo->getUserId()]);
        if ($hasTotpSecret) {
            // user is enrolled for 2FA, ask for it!
            return new HtmlResponse(
                $this->tpl->render(
                    'twoFactorTotp',
                    [
                        '_two_factor_user_id' => $userInfo->getUserId(),
                        '_two_factor_auth_invalid' => false,
                        '_two_factor_auth_redirect_to' => $request->getUri(),
                    ]
                )
            );
        }

        if ($this->requireTwoFactor) {
            // 2FA required, but user not enrolled, offer them to enroll
            $this->session->set('_two_factor_enroll_redirect_to', $request->getUri());

            return new RedirectResponse($request->getRootUri().'two_factor_enroll');
        }

        // 2FA not required, and user not enrolled...
        $this->session->regenerate();
        $this->session->set('_two_factor_verified', $userInfo->getUserId());

        return true;
    }
}
