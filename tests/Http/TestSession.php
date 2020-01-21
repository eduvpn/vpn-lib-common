<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Tests\Http;

use LC\Common\Http\SessionInterface;

class TestSession implements SessionInterface
{
    /** @var array */
    private $sessionData = [];

    /**
     * @return string
     */
    public function id()
    {
        return '12345';
    }

    /**
     * @return void
     */
    public function regenerate()
    {
        // NOP
    }

    /**
     * @param string $sessionKey
     * @param string $sessionValue
     *
     * @return void
     */
    public function set($sessionKey, $sessionValue)
    {
        $this->sessionData[$sessionKey] = $sessionValue;
    }

    /**
     * @param string $sessionKey
     *
     * @return void
     */
    public function remove($sessionKey)
    {
        if (\array_key_exists($sessionKey, $this->sessionData)) {
            unset($this->sessionData[$sessionKey]);
        }
    }

    /**
     * @param string $sessionKey
     *
     * @return string|null
     */
    public function get($sessionKey)
    {
        if (!\array_key_exists($sessionKey, $this->sessionData)) {
            return null;
        }
        $sessionValue = $this->sessionData[$sessionKey];
        if (!\is_string($sessionValue)) {
            return null;
        }

        return $sessionValue;
    }

    /**
     * @return void
     */
    public function destroy()
    {
        $this->sessionData = [];
    }
}
