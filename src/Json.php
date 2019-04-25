<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common;

use LC\Common\Exception\JsonException;

class Json
{
    /**
     * @param mixed $jsonData
     *
     * @return string
     */
    public static function encode($jsonData)
    {
        $jsonString = json_encode($jsonData);
        // 5.5.0 	The return value on failure was changed from null string to FALSE.
        if (false === $jsonString || 'null' === $jsonString) {
            throw new JsonException(
                sprintf(
                    'json_encode: %s',
                    json_last_error_msg()
                )
            );
        }

        return $jsonString;
    }

    /**
     * @param string $jsonString
     *
     * @return mixed
     */
    public static function decode($jsonString)
    {
        $jsonData = json_decode($jsonString, true);
        if (null === $jsonData && JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonException(
                sprintf(
                    'json_decode: %s',
                    json_last_error_msg()
                )
            );
        }

        return $jsonData;
    }
}
