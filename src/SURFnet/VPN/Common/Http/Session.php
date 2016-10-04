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
    /** @var array */
    private $sessionOptions;

    public function __construct($serverName, $requestRoot, $secureOnly)
    {
        session_set_cookie_params(
            [
                'lifetime' => 0,
                'path' => $requestRoot,
                'domain' => $serverName,
                'secure' => $secureOnly,
                'httponly' => true,
            ]
        );

        session_start();

        // Make sure we have a canary set
        if (!isset($_SESSION['canary'])) {
            session_regenerate_id(true);
            $_SESSION['canary'] = time();
            $_SESSION['serverName'] = $serverName;
            $_SESSION['requestRoot'] = $requestRoot;
        }
        // Regenerate session ID every five minutes:
        if ($_SESSION['canary'] < time() - 300) {
            session_regenerate_id(true);
            $_SESSION['canary'] = time();
            $_SESSION['serverName'] = $serverName;
            $_SESSION['requestRoot'] = $requestRoot;
        }

        if ($serverName !== $_SESSION['serverName']) {
            throw new HttpException('session error (serverName)', 400);
        }

        if ($requestRoot !== $_SESSION['requestRoot']) {
            throw new HttpException('session error (requestRoot)', 400);
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function delete($key)
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function has($key)
    {
        return array_key_exists($key, $_SESSION);
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }

        return;
    }

    public function destroy()
    {
        session_destroy();
    }
}
