<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Tests;

use PHPUnit_Framework_TestCase;
use SURFnet\VPN\Common\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleConfig()
    {
        $c = new Config(
            [
                'foo' => 'bar',
            ]
        );
        $this->assertSame('bar', $c->getItem('foo'));
    }

    public function testNestedConfig()
    {
        $c = new Config(
            [
                'foo' => [
                    'bar' => 'baz',
                ],
            ]
        );
        $this->assertSame('baz', $c->getSection('foo')->getItem('bar'));
    }

    public function testNoParameters()
    {
        $configData = ['foo' => 'bar'];
        $c = new Config($configData);
        $this->assertSame($configData, $c->toArray());
    }

    public function testExists()
    {
        $c = new Config(['foo' => 'bar']);
        $this->assertTrue($c->hasItem('foo'));
        $this->assertFalse($c->hasItem('bar'));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Exception\ConfigException
     * @expectedExceptionMessage item "foo" not available
     */
    public function testMissingConfig()
    {
        $c = new Config([]);
        $c->getItem('foo');
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Exception\ConfigException
     * @expectedExceptionMessage item "baz" not available
     */
    public function testMissingNestedConfig()
    {
        $c = new Config(
            [
                'foo' => [
                    'bar' => 'baz',
                ],
            ]
        );
        $c->getSection('foo')->getItem('baz');
    }

    public function testFromFile()
    {
        $c = Config::fromFile(sprintf('%s/data/config.php', __DIR__));
        $this->assertSame('b', $c->getSection('bar')->getItem('a'));
    }

    public function testMyConfigDefaultValues()
    {
        $c = new MyConfig(['a' => ['b' => ['c' => 'd']]]);
        $this->assertSame(['baz'], $c->getSection('foo')->getSection('bar')->toArray());
        $this->assertSame(['b' => ['c' => 'd']], $c->getSection('a')->toArray());
    }
}
