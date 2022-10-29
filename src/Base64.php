<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common;

class Base64
{
    /**
     * @param string $inputStr
     * @return string
     */
    public static function encode($inputStr)
    {
        return sodium_bin2base64($inputStr, SODIUM_BASE64_VARIANT_ORIGINAL);
    }

    /**
     * @param string $inputStr
     * @return string
     */
    public static function decode($inputStr)
    {
        return sodium_base642bin($inputStr, SODIUM_BASE64_VARIANT_ORIGINAL);
    }
}
