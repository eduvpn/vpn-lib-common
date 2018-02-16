<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use DateTime;
use PDO;

class PdoAuth implements CredentialValidatorInterface
{
    /** @var \PDO */
    private $db;

    /** @var \DateTime */
    private $dateTime;

    public function __construct(PDO $db, DateTime $dateTime = null)
    {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ('sqlite' === $db->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            $db->query('PRAGMA foreign_keys = ON');
        }

        $this->db = $db;
        if (null === $dateTime) {
            $dateTime = new DateTime();
        }
        $this->dateTime = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($authUser, $authPass)
    {
        $stmt = $this->db->prepare(
            'SELECT
                password_hash
             FROM users
             WHERE
                user_id = :user_id'
        );

        $stmt->bindValue(':user_id', $authUser, PDO::PARAM_STR);
        $stmt->execute();
        $dbHash = $stmt->fetchColumn(0);
        $isVerified = password_verify($authPass, $dbHash);
        if ($isVerified) {
            // update the "last_authenticated_at" timestamp
            $stmt = $this->db->prepare(
                'UPDATE
                    users
                 SET
                    last_authenticated_at = :last_authenticated_at
                 WHERE
                    user_id = :user_id'
            );

            $stmt->bindValue(':user_id', $authUser, PDO::PARAM_STR);
            $stmt->bindValue(':last_authenticated_at', $this->dateTime->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->execute();
        }

        return $isVerified;
    }

    /**
     * @param string $userId
     * @param string $userPass
     *
     * @return void
     */
    public function add($userId, $userPass)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO
                users (user_id, password_hash, created_at)
            VALUES
                (:user_id, :password_hash, :created_at)'
        );

        $passwordHash = password_hash($userPass, PASSWORD_DEFAULT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $this->dateTime->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * @return void
     */
    public function init()
    {
        $queryList = [
            'CREATE TABLE IF NOT EXISTS users (
                user_id VARCHAR(255) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                created_at VARCHAR(255) NOT NULL,
                last_authenticated_at VARCHAR(255) DEFAULT NULL,
                UNIQUE(user_id)
            )',
        ];

        foreach ($queryList as $query) {
            $this->db->query($query);
        }
    }
}
