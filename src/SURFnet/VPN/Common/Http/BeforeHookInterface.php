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

interface BeforeHookInterface
{
    /**
     * Execute a hook before routing.
     *
     * @param Request $request  the HTTP request
     * @param array   $hookData results from previously called hooks where the
     *                          key is the name given to the hook and the value contains the result
     *
     * @return mixed can return all types, if the result is a Response or a
     *               subclass of it, it is immediately returned to the client
     */
    public function executeBefore(Request $request, array $hookData);
}
