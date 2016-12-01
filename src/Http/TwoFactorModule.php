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

use SURFnet\VPN\Common\Http\Exception\HttpException;
use SURFnet\VPN\Common\HttpClient\Exception\ApiException;
use SURFnet\VPN\Common\HttpClient\ServerClient;
use SURFnet\VPN\Common\TplInterface;

class TwoFactorModule implements ServiceModuleInterface
{
    /** @var \SURFnet\VPN\Common\HttpClient\ServerClient */
    private $serverClient;

    /** @var SessionInterface */
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
            '/_two_factor/auth/verify',
            function (Request $request, array $hookData) {
                if (!array_key_exists('auth', $hookData)) {
                    throw new HttpException('authentication hook did not run before', 500);
                }
                $userId = $hookData['auth'];

                $this->session->delete('_two_factor_verified');

                $otpKey = InputValidation::otpKey($request->getPostParameter('otpKey'));
                $redirectTo = $request->getPostParameter('_two_factor_auth_redirect_to');

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

                try {
                    $this->serverClient->verifyTotpKey($userId, $otpKey);
                    $this->session->set('_two_factor_verified', $userId);

                    return new RedirectResponse($redirectTo, 302);
                } catch (ApiException $e) {
                    // unable to validate the OTP
                    $response = new Response(200, 'text/html');
                    $response->setBody(
                        $this->tpl->render(
                            'twoFactorAuthentication',
                            [
                                '_two_factor_auth_invalid_key' => true,
                                '_two_factor_auth_redirect_to' => $redirectTo,
                            ]
                        )
                    );

                    return $response;
                }
            }
        );
    }
}
