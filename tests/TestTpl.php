<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests;

use SURFnet\VPN\Common\TplInterface;

class TestTpl implements TplInterface
{
    public function render($templateName, array $templateVariables)
    {
        return json_encode([$templateName => $templateVariables]);
    }
}
