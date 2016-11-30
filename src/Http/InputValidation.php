<?php
/**
 *  Copyright (C) 2016 SURFnet.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Http\Exception\HttpException;

class InputValidation
{
    /**
     * @return string
     */
    public static function displayName($displayName)
    {
        $displayName = filter_var($displayName, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);

        if (0 === mb_strlen($displayName)) {
            throw new HttpException('invalid "display_name"', 400);
        }

        return $displayName;
    }

    /**
     * @return string
     */
    public static function commonName($commonName)
    {
        if (1 !== preg_match('/^[a-fA-F0-9]{32}$/', $commonName)) {
            throw new HttpException('invalid "common_name"', 400);
        }

        return $commonName;
    }

    /**
     * @return string
     */
    public static function profileId($profileId)
    {
        if (1 !== preg_match('/^[a-zA-Z0-9]+$/', $profileId)) {
            throw new HttpException('invalid "profile_id"', 400);
        }

        return $profileId;
    }

    /**
     * @return string
     */
    public static function languageCode($languageCode)
    {
        $supportedLanguages = ['en_US', 'nl_NL', 'de_DE', 'fr_FR'];
        if (!in_array($setLanguage, $supportedLanguages)) {
            throw new HttpException('invalid "language_code"', 400);
        }

        return $languageCode;
    }

    /**
     * @return string
     */
    public static function totpSecret($totpSecret)
    {
        if (1 !== preg_match('/^[A-Z0-9]{16}$/', $totpSecret)) {
            throw new HttpException('invalid "totp_secret"', 400);
        }

        return $totpSecret;
    }

    /**
     * @return string
     */
    public static function totpKey($totpKey)
    {
        if (1 !== preg_match('/^[0-9]{6}$/', $totpKey)) {
            throw new HttpException('invalid "totp_key"', 400);
        }

        return $totpKey;
    }

    /**
     * @return string
     */
    public static function clientId($clientId)
    {
        if (1 !== preg_match('/^(?:[\x20-\x7E])+$/', $clientId)) {
            throw new HttpException('invalid "client_id"', 400);
        }

        return $clientId;
    }
}
