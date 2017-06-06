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

use fkooman\SeCookie\SessionInterface;
use SURFnet\VPN\Common\Http\Exception\HttpException;
use SURFnet\VPN\Common\HttpClient\ServerClient;
use SURFnet\VPN\Common\TplInterface;

class TwoFactorHook implements BeforeHookInterface
{
    /** @var \fkooman\SeCookie\SessionInterface; */
    private $session;

    /** @var \SURFnet\VPN\Common\TplInterface */
    private $tpl;

    /** @var \SURFnet\VPN\Common\HttpClient\ServerClient */
    private $serverClient;

    public function __construct(SessionInterface $session, TplInterface $tpl, ServerClient $serverClient)
    {
        $this->session = $session;
        $this->tpl = $tpl;
        $this->serverClient = $serverClient;
    }

    public function executeBefore(Request $request, array $hookData)
    {
        // some URIs are allowed as they are used for either logging in, or
        // verifying the OTP key
        $allowedUris = [
            '/_form/auth/verify',
            '/_form/auth/logout',
            '/_two_factor/auth/verify/totp',
            '/_two_factor/auth/verify/yubi',
            '/_oauth/token',
        ];

        if (in_array($request->getPathInfo(), $allowedUris) && 'POST' === $request->getRequestMethod()) {
            return false;
        }

        if (!array_key_exists('auth', $hookData)) {
            throw new HttpException('authentication hook did not run before', 500);
        }
        $userId = $hookData['auth'];

        if ($this->session->has('_two_factor_verified')) {
            if ($userId !== $this->session->get('_two_factor_verified')) {
                throw new HttpException('two-factor code not bound to authenticated user', 400);
            }

            return true;
        }

        $hasTotpSecret = $this->serverClient->get('has_totp_secret', ['user_id' => $userId]);
        $hasYubiId = $this->serverClient->get('has_yubi_key_id', ['user_id' => $userId]);

        // check if the user is enrolled for 2FA, if not we are fine, for this
        // session we assume we are verified!
        if (!$hasTotpSecret && !$hasYubiId) {
            $this->session->set('_two_factor_verified', $userId);

            return false;
        }

        // if not Yubi, then TOTP
        $templateName = $hasYubiId ? 'twoFactorYubiKeyOtp' : 'twoFactorTotp';

        // any other URL, enforce 2FA
        $response = new Response(200, 'text/html');
        $response->setBody(
            $this->tpl->render(
                $templateName,
                [
                    '_two_factor_auth_invalid' => false,
                    '_two_factor_auth_redirect_to' => $request->getUri(),
                ]
            )
        );

        return $response;
    }
}
