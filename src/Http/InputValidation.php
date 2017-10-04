<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use DateTime;
use SURFnet\VPN\Common\Http\Exception\InputValidationException;

class InputValidation
{
    /**
     * @param string $displayName
     *
     * @return string
     */
    public static function displayName($displayName)
    {
        self::requireUtf8($displayName, 'displayName');

        $displayName = filter_var($displayName, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);

        if (0 === mb_strlen($displayName)) {
            throw new InputValidationException('invalid "display_name"');
        }

        return $displayName;
    }

    /**
     * @param string $commonName
     *
     * @return string
     */
    public static function commonName($commonName)
    {
        if (1 !== preg_match('/^[a-fA-F0-9]{32}$/', $commonName)) {
            throw new InputValidationException('invalid "common_name"');
        }

        return $commonName;
    }

    /**
     * @param string $serverCommonName
     *
     * @return string
     */
    public static function serverCommonName($serverCommonName)
    {
        if (1 !== preg_match('/^[a-zA-Z0-9-.]+$/', $serverCommonName)) {
            throw new InputValidationException('invalid "server_common_name"');
        }

        return $serverCommonName;
    }

    /**
     * @param string $profileId
     *
     * @return string
     */
    public static function profileId($profileId)
    {
        if (1 !== preg_match('/^[a-zA-Z0-9-.]+$/', $profileId)) {
            throw new InputValidationException('invalid "profile_id"');
        }

        return $profileId;
    }

    /**
     * @param string $instanceId
     *
     * @return string
     */
    public static function instanceId($instanceId)
    {
        if (1 !== preg_match('/^[a-zA-Z0-9-.]+$/', $instanceId)) {
            throw new InputValidationException('invalid "instance_id"');
        }

        return $instanceId;
    }

    /**
     * @param string $totpSecret
     *
     * @return string
     */
    public static function totpSecret($totpSecret)
    {
        if (1 !== preg_match('/^[A-Z0-9]{16}$/', $totpSecret)) {
            throw new InputValidationException('invalid "totp_secret"');
        }

        return $totpSecret;
    }

    /**
     * @param string $yubiKeyOtp
     *
     * @return string
     */
    public static function yubiKeyOtp($yubiKeyOtp)
    {
        if (1 !== preg_match('/^[a-z]{44}$/', $yubiKeyOtp)) {
            throw new InputValidationException('invalid "yubi_key_otp"');
        }

        return $yubiKeyOtp;
    }

    /**
     * @param string $totpKey
     *
     * @return string
     */
    public static function totpKey($totpKey)
    {
        if (1 !== preg_match('/^[0-9]{6}$/', $totpKey)) {
            throw new InputValidationException('invalid "totp_key"');
        }

        return $totpKey;
    }

    /**
     * @param string $clientId
     *
     * @return string
     */
    public static function clientId($clientId)
    {
        if (1 !== preg_match('/^(?:[\x20-\x7E])+$/', $clientId)) {
            throw new InputValidationException('invalid "client_id"');
        }

        return $clientId;
    }

    /**
     * @param string $dateTime
     *
     * @return \DateTime
     */
    public static function dateTime($dateTime)
    {
        if (false === $dateTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime)) {
            throw new InputValidationException('invalid "date_time"');
        }

        return $dateTimeObj;
    }

    /**
     * @param string $userId
     *
     * @return string
     */
    public static function userId($userId)
    {
        self::requireUtf8($userId, 'userId');

        $userIdLength = mb_strlen($userId);
        if (0 >= $userIdLength || 256 < $userIdLength) {
            throw new InputValidationException('invalid length: 0 < userId <= 256');
        }

        return $userId;
    }

    /**
     * @param string $ipAddress
     *
     * @return string
     */
    public static function ipAddress($ipAddress)
    {
        if (false === filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            throw new InputValidationException('invalid "ip_address"');
        }

        // normalize the IP address (only makes a difference for IPv6)
        return inet_ntop(inet_pton($ipAddress));
    }

    /**
     * @param string $ip4
     *
     * @return string
     */
    public static function ip4($ip4)
    {
        if (false === filter_var($ip4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new InputValidationException('invalid "ip4"');
        }

        return $ip4;
    }

    /**
     * @param string $ip6
     *
     * @return string
     */
    public static function ip6($ip6)
    {
        if (false === filter_var($ip6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            throw new InputValidationException('invalid "ip6"');
        }

        // normalize the IPv6 address
        return inet_ntop(inet_pton($ip6));
    }

    /**
     * @param string $connectedAt
     *
     * @return int
     */
    public static function connectedAt($connectedAt)
    {
        if (!is_numeric($connectedAt) || 0 > (int) $connectedAt) {
            throw new InputValidationException('invalid "connected_at"');
        }

        return (int) $connectedAt;
    }

    /**
     * @param string $disconnectedAt
     *
     * @return int
     */
    public static function disconnectedAt($disconnectedAt)
    {
        if (!is_numeric($disconnectedAt) || 0 > (int) $disconnectedAt) {
            throw new InputValidationException('invalid "disconnected_at"');
        }

        return (int) $disconnectedAt;
    }

    /**
     * @param string $bytesTransferred
     *
     * @return int
     */
    public static function bytesTransferred($bytesTransferred)
    {
        if (!is_numeric($bytesTransferred) || 0 > (int) $bytesTransferred) {
            throw new InputValidationException('invalid "bytes_transferred"');
        }

        return (int) $bytesTransferred;
    }

    /**
     * @param string $twoFactorType
     *
     * @return string
     */
    public static function twoFactorType($twoFactorType)
    {
        if ('totp' !== $twoFactorType && 'yubi' !== $twoFactorType) {
            throw new InputValidationException('invalid "two_factor_type"');
        }

        return $twoFactorType;
    }

    /**
     * @param string $twoFactorValue
     *
     * @return string
     */
    public static function twoFactorValue($twoFactorValue)
    {
        if (!is_string($twoFactorValue) || 0 >= strlen($twoFactorValue)) {
            throw new InputValidationException('invalid "two_factor_value"');
        }

        return $twoFactorValue;
    }

    /**
     * @param string $messageId
     *
     * @return int
     */
    public static function messageId($messageId)
    {
        if (!is_numeric($messageId) || 0 >= $messageId) {
            throw new InputValidationException('invalid "message_id"');
        }

        return (int) $messageId;
    }

    /**
     * @param string $messageType
     *
     * @return string
     */
    public static function messageType($messageType)
    {
        if ('motd' !== $messageType && 'notification' !== $messageType && 'maintenance' !== $messageType) {
            throw new InputValidationException('invalid "message_type"');
        }

        return $messageType;
    }

    /**
     * @param string $inputString
     * @param string $inputName
     *
     * @return void
     */
    private static function requireUtf8($inputString, $inputName)
    {
        // we want valid UTF-8
        if (!mb_check_encoding($inputString, 'UTF-8')) {
            throw new InputValidationException(sprintf('invalid encoding for "%s"', $inputName));
        }
    }
}
