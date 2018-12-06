<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
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

    /** @var \fkooman\SeCookie\SessionInterface */
    private $session;

    /** @var \SURFnet\VPN\Common\TplInterface */
    private $tpl;

    public function __construct(ServerClient $serverClient, SessionInterface $session, TplInterface $tpl)
    {
        $this->serverClient = $serverClient;
        $this->session = $session;
        $this->tpl = $tpl;
    }

    /**
     * @return void
     */
    public function init(Service $service)
    {
        $service->post(
            '/_two_factor/auth/verify/totp',
            /**
             * @return Response
             */
            function (Request $request, array $hookData) {
                if (!array_key_exists('auth', $hookData)) {
                    throw new HttpException('authentication hook did not run before', 500);
                }
                $userInfo = $hookData['auth'];

                $this->session->delete('_two_factor_verified');

                $totpKey = InputValidation::totpKey($request->getPostParameter('_two_factor_auth_totp_key'));
                $redirectTo = $request->getPostParameter('_two_factor_auth_redirect_to');

                try {
                    $this->serverClient->post('verify_totp_key', ['user_id' => $userInfo->id(), 'totp_key' => $totpKey]);
                    $this->session->regenerate(true);
                    $this->session->set('_two_factor_verified', $userInfo->id());

                    return new RedirectResponse($redirectTo, 302);
                } catch (ApiException $e) {
                    // unable to validate the OTP
                    $response = new Response(200, 'text/html');
                    $response->setBody(
                        $this->tpl->render(
                            'twoFactorTotp',
                            [
                                '_two_factor_user_id' => $userInfo->id(),
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
}
