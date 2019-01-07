<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use fkooman\SeCookie\SessionInterface;

class LogoutModule implements ServiceModuleInterface
{
    /** @var \fkooman\SeCookie\SessionInterface */
    private $session;

    /** @var null|string */
    private $logoutUrl;

    /**
     * @param \fkooman\SeCookie\SessionInterface $session
     * @param null|string                        $logoutUrl
     */
    public function __construct(SessionInterface $session, $logoutUrl = null)
    {
        $this->session = $session;
        $this->logoutUrl = $logoutUrl;
    }

    /**
     * @param \SURFnet\VPN\Common\Http\Service $service
     *
     * @return void
     */
    public function init(Service $service)
    {
        // new URL since we introduce SAML / Mellon logout
        $service->post(
            '/_logout',
            /**
             * @return \SURFnet\VPN\Common\Http\Response
             */
            function (Request $request, array $hookData) {
                $httpReferrer = $request->requireHeader('HTTP_REFERER');
                if (null !== $this->logoutUrl) {
                    // a logout URL is defined, this is used by SAML/Mellon
                    return new RedirectResponse(
                        sprintf(
                            '%s?%s',
                            $this->logoutUrl,
                            http_build_query(
                                [
                                    'ReturnTo' => $httpReferrer,
                                ]
                            )
                        )
                    );
                }

                // we only destroy the session here, and not for the
                // "SAML backends" as they may store stuff in the session as
                // well, defer to those authtication mechanisms...
                $this->session->destroy();

                return new RedirectResponse($httpReferrer);
            }
        );
    }
}
