<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests\Http;

use DateTime;
use PDO;
use PHPUnit\Framework\TestCase;
use SURFnet\VPN\Common\Http\PdoAuth;

class PdoAuthTest extends TestCase
{
    /** @var PdoAuth */
    private $pdoAuth;

    public function setUp()
    {
        $dateTime = new DateTime('2018-01-01 13:37:00');
        $db = new PDO('sqlite::memory:');
        $this->pdoAuth = new PdoAuth($db, $dateTime);
        $this->pdoAuth->init();
    }

    public function testValid()
    {
        $this->pdoAuth->add('foo', 'bar');
        $this->assertTrue($this->pdoAuth->isValid('foo', 'bar'));
    }

    public function testInvalidPass()
    {
        $this->pdoAuth->add('foo', 'bar');
        $this->assertFalse($this->pdoAuth->isValid('foo', 'baz'));
    }

    public function testInvalidUser()
    {
        $this->pdoAuth->add('foo', 'bar');
        $this->assertFalse($this->pdoAuth->isValid('fop', 'bar'));
    }

    public function testInvalidUserPass()
    {
        $this->pdoAuth->add('foo', 'bar');
        $this->assertFalse($this->pdoAuth->isValid('fop', 'baz'));
    }
}
