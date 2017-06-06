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
use SURFnet\VPN\Common\HttpClient\Exception\ApiException;
use SURFnet\VPN\Common\HttpClient\ServerClient;
use SURFnet\VPN\Common\TplInterface;

class TwoFactorModule implements ServiceModuleInterface
{
    /** @var \SURFnet\VPN\Common\HttpClient\ServerClient */
    private $serverClient;

    /** @var \fkooman\SeCookie\SessionInterface; */
    private $session;

    /** @var \SURFnet\VPN\Common\TplInterface */
    private $tpl;

    public function __construct(ServerClient $serverClient, SessionInterface $session, TplInterface $tpl)
    {
        $this->serverClient = $serverClient;
        $this->session = $session;
        $this->tpl = $tpl;
    }

    public function init(Service $service)
    {
        $service->post(
            '/_two_factor/auth/verify/totp',
            function (Request $request, array $hookData) {
                if (!array_key_exists('auth', $hookData)) {
                    throw new HttpException('authentication hook did not run before', 500);
                }
                $userId = $hookData['auth'];

                $this->session->delete('_two_factor_verified');

                $totpKey = InputValidation::totpKey($request->getPostParameter('_two_factor_auth_totp_key'));
                $redirectTo = $request->getPostParameter('_two_factor_auth_redirect_to');
                self::validateRedirectTo($request, $redirectTo);

                try {
                    $this->serverClient->post('verify_totp_key', ['user_id' => $userId, 'totp_key' => $totpKey]);
                    $this->session->regenerate(true);
                    $this->session->set('_two_factor_verified', $userId);

                    return new RedirectResponse($redirectTo, 302);
                } catch (ApiException $e) {
                    // unable to validate the OTP
                    $response = new Response(200, 'text/html');
                    $response->setBody(
                        $this->tpl->render(
                            'twoFactorTotp',
                            [
                                '_two_factor_auth_invalid' => true,
                                '_two_factor_auth_error_msg' => $e->getMessage(),
                                '_two_factor_auth_redirect_to' => $redirectTo,
                            ]
                        )
                    );

                    return $response;
                }
            }
        );

        $service->post(
            '/_two_factor/auth/verify/yubi',
            function (Request $request, array $hookData) {
                if (!array_key_exists('auth', $hookData)) {
                    throw new HttpException('authentication hook did not run before', 500);
                }
                $userId = $hookData['auth'];

                $this->session->delete('_two_factor_verified');

                $yubiKeyOtp = InputValidation::yubiKeyOtp($request->getPostParameter('_two_factor_auth_yubi_key_otp'));
                $redirectTo = $request->getPostParameter('_two_factor_auth_redirect_to');
                self::validateRedirectTo($request, $redirectTo);

                try {
                    $this->serverClient->post('verify_yubi_key_otp', ['user_id' => $userId, 'yubi_key_otp' => $yubiKeyOtp]);
                    $this->session->regenerate(true);
                    $this->session->set('_two_factor_verified', $userId);

                    return new RedirectResponse($redirectTo, 302);
                } catch (ApiException $e) {
                    // unable to validate the OTP
                    $response = new Response(200, 'text/html');
                    $response->setBody(
                        $this->tpl->render(
                            'twoFactorYubiKeyOtp',
                            [
                                '_two_factor_auth_invalid' => true,
                                '_two_factor_auth_error_msg' => $e->getMessage(),
                                '_two_factor_auth_redirect_to' => $redirectTo,
                            ]
                        )
                    );

                    return $response;
                }
            }
        );
    }

    private static function validateRedirectTo(Request $request, $redirectTo)
    {
        // validate the URL
        if (false === filter_var($redirectTo, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED | FILTER_FLAG_PATH_REQUIRED)) {
            throw new HttpException('invalid redirect_to URL', 400);
        }

        // extract the "host" part of the URL
        if (false === $redirectToHost = parse_url($redirectTo, PHP_URL_HOST)) {
            throw new HttpException('invalid redirect_to URL, unable to extract host', 400);
        }
        if ($request->getServerName() !== $redirectToHost) {
            throw new HttpException('redirect_to does not match expected host', 400);
        }
    }
}
