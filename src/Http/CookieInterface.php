<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Http;

interface CookieInterface
{
    /**
     * @param string $cookieName
     * @param string $cookieValue
     *
     * @return void
     */
    public function set($cookieName, $cookieValue);
}
