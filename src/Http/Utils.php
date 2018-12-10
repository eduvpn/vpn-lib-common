<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Http\Exception\HttpException;

class Utils
{
    /**
     * @param Request                     $request
     * @param array<string,array<string>> $whiteList
     *
     * @return bool
     */
    public static function getInWhitelist(Request $request, array $whiteList)
    {
        $requestMethod = $request->getRequestMethod();
        if (!array_key_exists($requestMethod, $whiteList)) {
            return false;
        }

        if (!\in_array($request->getPathInfo(), $whiteList[$requestMethod], true)) {
            return false;
        }

        return true;
    }

    /**
     * @param array  $sourceData
     * @param string $key
     * @param bool   $isRequired
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public static function getValueFromArray(array $sourceData, $key, $isRequired, $defaultValue)
    {
        if (array_key_exists($key, $sourceData)) {
            return $sourceData[$key];
        }

        if ($isRequired) {
            throw new HttpException(
                sprintf('missing required field "%s"', $key),
                400
            );
        }

        return $defaultValue;
    }
}
