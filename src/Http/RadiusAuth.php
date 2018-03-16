<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use Psr\Log\LoggerInterface;
use SURFnet\VPN\Common\Http\Exception\RadiusException;

class RadiusAuth implements CredentialValidatorInterface
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var string */
    private $host;

    /** @var string */
    private $secret;

    /** @var int */
    private $port = 1812;

    /** @var string|null */
    private $realm = null;

    /** @var string|null */
    private $nasIdentifier = null;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string                   $host
     * @param string                   $secret
     */
    public function __construct(LoggerInterface $logger, $host, $secret)
    {
        $this->logger = $logger;
        $this->host = $host;
        $this->secret = $secret;
    }

    /**
     * @param int $port
     *
     * @return void
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @param string $realm
     *
     * @return void
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    /**
     * @param string $nasIdentifier
     *
     * @return void
     */
    public function setNasIdentifier($nasIdentifier)
    {
        $this->nasIdentifier = $nasIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($authUser, $authPass)
    {
        // add realm if requested
        if (null !== $this->realm) {
            $authUser = sprintf('%s@%s', $authUser, $this->realm);
        }

        $radiusAuth = radius_auth_open();
        if (false === radius_add_server(
            $radiusAuth,
            $this->host,
            $this->port,
            $this->secret,
            5,  // timeout
            3   // max_tries
        )) {
            $errorMsg = sprintf('RADIUS error: %s', radius_strerror($radiusAuth));
            $this->logger->error($errorMsg);

            throw new RadiusException($errorMsg);
        }

        if (false === radius_create_request($radiusAuth, RADIUS_ACCESS_REQUEST)) {
            $errorMsg = sprintf('RADIUS error: %s', radius_strerror($radiusAuth));
            $this->logger->error($errorMsg);

            throw new RadiusException($errorMsg);
        }

        radius_put_attr($radiusAuth, RADIUS_USER_NAME, $authUser);
        radius_put_attr($radiusAuth, RADIUS_USER_PASSWORD, $authPass);
        if (null !== $this->nasIdentifier) {
            radius_put_attr($radiusAuth, RADIUS_NAS_IDENTIFIER, $this->nasIdentifier);
        }

        if (RADIUS_ACCESS_ACCEPT === radius_send_request($radiusAuth)) {
            return true;
        }

        if (RADIUS_ACCESS_REJECT === radius_send_request($radiusAuth)) {
            // wrong authUser/authPass
            return false;
        }

        $errorMsg = sprintf('RADIUS error: %s', radius_strerror($radiusAuth));
        $this->logger->error($errorMsg);

        throw new RadiusException($errorMsg);
    }
}
