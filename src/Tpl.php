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
     * @param null|string   $localeFile
     */
    public function __construct(array $templateDirs, $localeFile)
    {
        $this->tpl = new Template($templateDirs, $localeFile);
        $this->tpl->addCallback('bytes_to_human', [\get_class($this), 'toHuman']);
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

    /**
     * @param int $byteSize
     *
     * @return string
     */
    public static function toHuman($byteSize)
    {
        $kB = 1024;
        $MB = $kB * 1024;
        $GB = $MB * 1024;
        $TB = $GB * 1024;
        if ($byteSize > $TB) {
            return sprintf('%0.2f TiB', $byteSize / $TB);
        }
        if ($byteSize > $GB) {
            return sprintf('%0.2f GiB', $byteSize / $GB);
        }
        if ($byteSize > $MB) {
            return sprintf('%0.2f MiB', $byteSize / $MB);
        }

        return sprintf('%0.0f kiB', $byteSize / $kB);
    }
}
