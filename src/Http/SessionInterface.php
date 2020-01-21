<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Http;

interface SessionInterface
{
    /**
     * @return void
     */
    public function regenerate();

    /**
     * @param string $sessionKey
     *
     * @return string|null
     */
    public function get($sessionKey);

    /**
     * @param string $sessionKey
     * @param string $sessionValue
     *
     * @return void
     */
    public function set($sessionKey, $sessionValue);

    /**
     * @param string $sessionKey
     *
     * @return void
     */
    public function remove($sessionKey);

    /**
     * @return void
     */
    public function destroy();
}
