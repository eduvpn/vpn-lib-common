<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common;

use fkooman\Tpl\Template;

class Tpl implements TplInterface
{
    /** @var \fkooman\Tpl\Template */
    private $tpl;

    /**
     * @param array<string> $templateDirs
     * @param string        $localeFile
     */
    public function __construct(array $templateDirs, $localeFile)
    {
        $this->tpl = new Template($templateDirs, $localeFile);
    }

    /**
     * @return void
     */
    public function addDefault(array $templateVariables)
    {
        $this->tpl->addDefault($templateVariables);
    }

    /**
     * @param string $templateName
     * @param array  $templateVariables
     *
     * @return string
     */
    public function render($templateName, array $templateVariables)
    {
        return $this->tpl->render(
            $templateName,
            $templateVariables
        );
    }
}
