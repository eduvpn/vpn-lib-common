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

class CallbackHook implements BeforeHookInterface, AfterHookInterface
{
    /** @var callable|null */
    private $before;

    /** @var callable|null */
    private $after;

    public function __construct(callable $before = null, callable $after = null)
    {
        $this->before = $before;
        $this->after = $after;
    }

    public function executeBefore(Request $request, array $hookData)
    {
        if (!is_null($this->before)) {
            return call_user_func($this->before, $request, $hookData);
        }
    }

    public function executeAfter(Request $request, Response $response)
    {
        if (!is_null($this->after)) {
            return call_user_func($this->after, $request, $response);
        }

        return $response;
    }
}
