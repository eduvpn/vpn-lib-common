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

class Session implements SessionInterface
{
    /** @var string */
    private $ns;

    /** @var array */
    private $sessionOptions;

    public function __construct($ns = 'MySession', array $sessionOptions = [])
    {
        $this->ns = $ns;

        $defaultOptions = [
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
        ];

        $this->sessionOptions = array_merge($defaultOptions, $sessionOptions);
    }

    /**
     * Start the session.
     *
     * We only start the session when it is actually being used by any of the
     * session methods.
     */
    private function startSession()
    {
        if ('' === session_id()) {
            // no session active
            session_set_cookie_params(
                $this->sessionOptions['lifetime'],
                $this->sessionOptions['path'],
                $this->sessionOptions['domain'],
                $this->sessionOptions['secure'],
                $this->sessionOptions['httponly']
            );
            session_start();
        }
    }

    public function set($key, $value)
    {
        $this->startSession();
        $_SESSION[$this->ns][$key] = $value;
    }

    public function delete($key)
    {
        $this->startSession();
        if ($this->has($key)) {
            unset($_SESSION[$this->ns][$key]);
        }
    }

    public function has($key)
    {
        $this->startSession();
        if (array_key_exists($this->ns, $_SESSION)) {
            return array_key_exists($key, $_SESSION[$this->ns]);
        }

        return false;
    }

    public function get($key)
    {
        $this->startSession();
        if ($this->has($key)) {
            return $_SESSION[$this->ns][$key];
        }

        return;
    }

    public function destroy()
    {
        $this->startSession();
        if (array_key_exists($this->ns, $_SESSION)) {
            unset($_SESSION[$this->ns]);
        }
    }
}
