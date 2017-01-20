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

class Session implements SessionInterface
{
    /** @var string */
    private $serverName;

    /** @var string */
    private $requestRoot;

    /** @var bool */
    private $secureOnly;

    public function __construct($serverName, $requestRoot, $secureOnly)
    {
        $this->serverName = $serverName;
        $this->requestRoot = $requestRoot;
        $this->secureOnly = $secureOnly;
    }

    public function set($key, $value)
    {
        $this->startSession();
        $_SESSION[$key] = $value;
    }

    public function delete($key)
    {
        $this->startSession();
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function has($key)
    {
        $this->startSession();

        return array_key_exists($key, $_SESSION);
    }

    public function get($key)
    {
        $this->startSession();
        if ($this->has($key)) {
            return $_SESSION[$key];
        }
    }

    public function destroy()
    {
        if ('' !== session_id()) {
            // session already started
            return;
        }
        session_destroy();
    }

    private function startSession()
    {
        if ('' !== session_id()) {
            // session already started
            return;
        }

        session_set_cookie_params(0, $this->requestRoot, $this->serverName, $this->secureOnly, true);
        session_start();

        // Make sure we have a canary set
        if (!isset($_SESSION['canary'])) {
            $this->setCanary($this->serverName, $this->requestRoot);
        }
        // Regenerate session ID every five minutes:
        if ($_SESSION['canary'] < time() - 300) {
            $this->setCanary($this->serverName, $this->requestRoot);
        }

        if ($this->serverName !== $_SESSION['serverName']) {
            throw new HttpException('session error (serverName)', 400);
        }

        if ($this->requestRoot !== $_SESSION['requestRoot']) {
            throw new HttpException('session error (requestRoot)', 400);
        }
    }

    private function setCanary($serverName, $requestRoot)
    {
        session_regenerate_id(true);
        $_SESSION['canary'] = time();
        $_SESSION['serverName'] = $serverName;
        $_SESSION['requestRoot'] = $requestRoot;
    }
}
