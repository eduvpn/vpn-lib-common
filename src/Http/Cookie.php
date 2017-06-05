<?php
/**
 *  Copyright (C) 2017 SURFnet.
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

class Cookie
{
    /** @var array */
    private $cookieOptions;

    /**
     * @param array $cookieOptions
     */
    public function __construct(array $cookieOptions = [])
    {
        $this->cookieOptions = array_merge(
            [
                // defaults
                'Secure' => true,       // bool
                'HttpOnly' => true,     // bool
                'Path' => '/',          // string
                'Domain' => null,       // string
                'Max-Age' => null,      // int > 0
                'SameSite' => 'Strict', // "Strict|Lax"
            ],
            $cookieOptions
        );
    }

    public function delete($name)
    {
        self::set($name, '');
    }

    public function set($name, $value)
    {
        $attributeValueList = [];
        if ($this->cookieOptions['Secure']) {
            $attributeValueList[] = 'Secure';
        }
        if ($this->cookieOptions['HttpOnly']) {
            $attributeValueList[] = 'HttpOnly';
        }
        $attributeValueList[] = sprintf('Path=%s', $this->cookieOptions['Path']);
        if (!is_null($this->cookieOptions['Domain'])) {
            $attributeValueList[] = sprintf('Domain=%s', $this->cookieOptions['Domain']);
        }

        if (!is_null($this->cookieOptions['Max-Age'])) {
            $attributeValueList[] = sprintf('Max-Age=%d', $this->cookieOptions['Max-Age']);
        }
        $attributeValueList[] = sprintf('SameSite=%s', $this->cookieOptions['SameSite']);

        header(
            sprintf(
                'Set-Cookie: %s=%s; %s',
                $name,
                $value,
                implode('; ', $attributeValueList)
            ),
            false
        );
    }

    /**
     * Replace an existing HTTP cookie.
     *
     * @param string $name  the cookie name
     * @param string $value the cookie value
     */
    protected function replace($name, $value)
    {
        $cookieList = [];
        foreach (headers_list() as $hdr) {
            if (0 === stripos($hdr, 'Set-Cookie: ')) {
                // found "Set-Cookie"
                if (0 !== stripos($hdr, sprintf('Set-Cookie: %s=%s', $name, $value))) {
                    // not the one we want to replace, add to backup list
                    $cookieList[] = $hdr;
                }
            }
        }
        // remove all "Set-Cookie" headers, `header_remove()` is case
        // insensitive
        header_remove('Set-Cookie');

        // restore cookies we want to keep
        foreach ($cookieList as $cookie) {
            header($cookie, false);
        }

        self::set($name, $value);
    }
}
