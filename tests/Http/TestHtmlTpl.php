<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

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
