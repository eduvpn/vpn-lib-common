<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Tests;

use LC\Common\Config;
use LC\Common\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @return void
     */
    public function testSimpleConfig()
    {
        $c = new Config(
            [
                'foo' => 'bar',
            ]
        );
        $this->assertSame('bar', $c->getItem('foo'));
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testNoParameters()
    {
        $configData = ['foo' => 'bar'];
        $c = new Config($configData);
        $this->assertSame($configData, $c->toArray());
    }

    /**
     * @return void
     */
    public function testExists()
    {
        $c = new Config(['foo' => 'bar']);
        $this->assertTrue($c->hasItem('foo'));
        $this->assertFalse($c->hasItem('bar'));
    }

    /**
     * @return void
     */
    public function testMissingConfig()
    {
        try {
            $c = new Config([]);
            $c->getItem('foo');
            self::fail();
        } catch (ConfigException $e) {
            self::assertSame('item "foo" not available', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testMissingNestedConfig()
    {
        try {
            $c = new Config(
                [
                    'foo' => [
                        'bar' => 'baz',
                    ],
                ]
            );
            $c->getSection('foo')->getItem('baz');
            self::fail();
        } catch (ConfigException $e) {
            self::assertSame('item "baz" not available', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testFromFile()
    {
        $c = Config::fromFile(sprintf('%s/data/config.php', __DIR__));
        $this->assertSame('b', $c->getSection('bar')->getItem('a'));
    }

    /**
     * @return void
     */
    public function testMyConfigDefaultValues()
    {
        $c = new MyConfig(['a' => ['b' => ['c' => 'd']]]);
        $this->assertSame(['baz'], $c->getSection('foo')->getSection('bar')->toArray());
        $this->assertSame(['b' => ['c' => 'd']], $c->getSection('a')->toArray());
    }
}
