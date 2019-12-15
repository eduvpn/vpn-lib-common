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
     * @return bool
     */
    public function has($sessionKey);

    /**
     * @param string $sessionKey
     *
     * @return string
     */
    public function getString($sessionKey);

    /**
     * @param string $sessionKey
     * @param string $sessionValue
     *
     * @return void
     */
    public function setString($sessionKey, $sessionValue);

    /**
     * @param string $sessionKey
     *
     * @return array<string>
     */
    public function getStringArray($sessionKey);

    /**
     * @param string        $sessionKey
     * @param array<string> $sessionValue
     *
     * @return void
     */
    public function setStringArray($sessionKey, array $sessionValue);

    /**
     * @param string $sessionKey
     *
     * @return bool
     */
    public function getBool($sessionKey);

    /**
     * @param string $sessionKey
     * @param bool   $sessionValue
     *
     * @return void
     */
    public function setBool($sessionKey, $sessionValue);

    /**
     * @param string $sessionKey
     *
     * @return void
     */
    public function delete($sessionKey);

    /**
     * @return void
     */
    public function destroy();
}
