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
     * @param string $authUser
     * @param string $authPass
     *
     * @return false|UserInfo
     */
    public function isValid($authUser, $authPass)
    {
        $stmt = $this->db->prepare(
            'SELECT
                password_hash, is_admin
             FROM users
             WHERE
                user_id = :user_id'
        );

        $stmt->bindValue(':user_id', $authUser, PDO::PARAM_STR);
        $stmt->execute();

        $dbResult = $stmt->fetch(PDO::FETCH_ASSOC);

        $dbHash = $dbResult['password_hash'];
        if (false === password_verify($authPass, $dbHash)) {
            return false;
        }

        $isAdmin = (bool) $dbResult['is_admin'];
        $entitlementList = [];
        if ($isAdmin) {
            $entitlementList[] = 'admin';
        }

        $userInfo = new UserInfo($authUser, $entitlementList);

        return $userInfo;
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
                users (user_id, password_hash, is_admin, created_at)
            VALUES
                (:user_id, :password_hash, :is_admin, :created_at)'
        );

        $passwordHash = password_hash($userPass, PASSWORD_DEFAULT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':is_admin', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':created_at', $this->dateTime->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * @param string $authUser
     *
     * @return bool
     */
    public function userExists($authUser)
    {
        $stmt = $this->db->prepare(
            'SELECT
                COUNT(*)
             FROM users
             WHERE
                user_id = :user_id'
        );

        $stmt->bindValue(':user_id', $authUser, PDO::PARAM_STR);
        $stmt->execute();

        return 1 === (int) $stmt->fetchColumn();
    }

    /**
     * @param string $userId
     * @param string $newUserPass
     *
     * @return bool
     */
    public function updatePassword($userId, $newUserPass)
    {
        $stmt = $this->db->prepare(
            'UPDATE
                users
             SET
                password_hash = :password_hash
             WHERE
                user_id = :user_id'
        );

        $passwordHash = password_hash($newUserPass, PASSWORD_DEFAULT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
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
                is_admin BOOLEAN NOT NULL,
                created_at VARCHAR(255) NOT NULL,
                UNIQUE(user_id)
            )',
        ];

        foreach ($queryList as $query) {
            $this->db->query($query);
        }
    }
}
