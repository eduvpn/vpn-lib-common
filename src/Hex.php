<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common;

class Hex
{
    /**
     * @param string $inputStr
     * @return string
     */
    public static function encode($inputStr)
    {
        return sodium_bin2hex($inputStr);
    }

    /**
     * @param string $inputStr
     * @return string
     */
    public static function decode($inputStr)
    {
        return sodium_hex2bin($inputStr);
    }
}
