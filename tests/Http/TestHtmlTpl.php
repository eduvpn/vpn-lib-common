<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Tests\Http;

use LC\Common\TplInterface;

class TestHtmlTpl implements TplInterface
{
    /**
     * @param array $templateVariables
     *
     * @return void
     */
    public function addDefault(array $templateVariables)
    {
    }

    /**
     * @param string $templateName
     * @param array  $templateVariables
     *
     * @return string
     */
    public function render($templateName, array $templateVariables)
    {
        $str = '<html><head><title>{{code}}</title></head><body><h1>Error ({{code}})</h1><p>{{message}}</p></body></html>';
        foreach ($templateVariables as $k => $v) {
            $str = str_replace(sprintf('{{%s}}', $k), $v, $str);
        }

        return $str;
    }
}
