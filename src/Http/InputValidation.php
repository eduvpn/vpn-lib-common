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
    public static function serverCommonName($serverCommonName)
    {
        if (1 !== preg_match('/^[a-zA-Z0-9-.]+$/', $serverCommonName)) {
            throw new HttpException('invalid "server_common_name"', 400);
        }

        return $serverCommonName;
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
    public static function instanceId($instanceId)
    {
        if (1 !== preg_match('/^[a-zA-Z0-9-.]+$/', $serverCommonName)) {
            throw new HttpException('invalid "instance_id"', 400);
        }

        return $instanceId;
    }

    /**
     * @return string
     */
    public static function languageCode($languageCode)
    {
        $supportedLanguages = ['en_US', 'nl_NL', 'de_DE', 'fr_FR'];
        if (!in_array($languageCode, $supportedLanguages)) {
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

    /**
     * @return string
     */
    public static function userId($userId)
    {
        if (1 !== preg_match('/^[a-zA-Z0-9-.@]+$/', $userId)) {
            throw new HttpException('invalid "user_id"', 400);
        }

        return $userId;
    }

    /**
     * @return string
     */
    public static function motdMessage($motdMessage)
    {
        // we accept everything...
        return $motdMessage;
    }

    /**
     * @return int
     */
    public static function dateTime($dateTime)
    {
        // try to parse first
        if (false === $unixTime = strtotime($dateTime)) {
            // if that fails, check if it is already unixTime
            $unixTime = intval($dateTime);
            if (0 <= $unixTime) {
                return $unixTime;
            }

            throw new HttpException('invalid "date_time"', 400);
        }

        return $unixTime;
    }

    /**
     * @return string
     */
    public static function ipAddress($ipAddress)
    {
        if (false === filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            throw new HttpException('invalid "ip_address"', 400);
        }

        // normalize the IP address (only makes a difference for IPv6)
        return inet_ntop(inet_pton($ipAddress));
    }

    /**
     * @return string
     */
    public static function vootToken($vootToken)
    {
        if (1 !== preg_match('/^[a-zA-Z0-9-]+$/', $vootToken)) {
            throw new HttpException('invalid "voot_token"', 400);
        }

        return $vootToken;
    }

    /**
     * @return string
     */
    public static function ip4($ip4)
    {
        if (false === filter_var($ip4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new HttpException('invalid "ip4"', 400);
        }

        return $ip4;
    }

    /**
     * @return string
     */
    public static function ip6($ip6)
    {
        if (false === filter_var($ip6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            throw new HttpException('invalid "ip6"', 400);
        }

        // normalize the IPv6 address
        return inet_ntop(inet_pton($ip6));
    }

    /**
     * @return int
     */
    public static function connectedAt($connectedAt)
    {
        if (!is_numeric($connectedAt) || 0 > intval($connectedAt)) {
            throw new HttpException('invalid "connected_at"', 400);
        }

        return intval($connectedAt);
    }

    /**
     * @return int
     */
    public static function disconnectedAt($disconnectedAt)
    {
        if (!is_numeric($disconnectedAt) || 0 > intval($disconnectedAt)) {
            throw new HttpException('invalid "disconnected_at"', 400);
        }

        return intval($disconnectedAt);
    }

    /**
     * @return int
     */
    public static function bytesTransferred($bytesTransferred)
    {
        if (!is_numeric($bytesTransferred) || 0 > intval($bytesTransferred)) {
            throw new HttpException('invalid "bytes_transferred"', 400);
        }

        return intval($bytesTransferred);
    }

    /**
     * @return string
     */
    public static function userName($userName)
    {
        if ('totp' !== $userName) {
            throw new InputValidationException('invalid "user_name"');
        }

        return $userName;
    }
}
