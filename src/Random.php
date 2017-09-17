<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common;

class Random implements RandomInterface
{
    /**
     * @param int $length
     *
     * @return string
     */
    public function get($length)
    {
        return \Sodium\bin2hex(
            \Sodium\randombytes_buf($length)
        );
    }
}
