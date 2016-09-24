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
namespace SURFnet\VPN\Common\Http\Test;

use SURFnet\VPN\Common\TplInterface;

class TestHtmlTpl implements TplInterface
{
    public function render($templateName, array $templateVariables)
    {
        $str = '<html><head><title>{{code}}</title></head><body><h1>Error ({{code}})</h1><p>{{message}}</p></body></html>';
        foreach ($templateVariables as $k => $v) {
            $str = str_replace(sprintf('{{%s}}', $k), $v, $str);
        }

        return $str;
    }
}
