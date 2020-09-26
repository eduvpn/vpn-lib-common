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
        $this->assertSame('bar', $c->requireString('foo'));
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
        $this->assertSame('baz', $c->s('foo')->requireString('bar'));
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
        $this->assertNotNull($c->requireString('foo'));
        $this->assertNull($c->optionalString('bar'));
    }

    /**
     * @return void
     */
    public function testMissingConfig()
    {
        try {
            $c = new Config([]);
            $c->requireString('foo');
            self::fail();
        } catch (ConfigException $e) {
            self::assertSame('key "foo" not available', $e->getMessage());
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
            $c->s('foo')->requireString('baz');
            self::fail();
        } catch (ConfigException $e) {
            self::assertSame('key "baz" not available', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testFromFile()
    {
        $c = Config::fromFile(sprintf('%s/data/config.php', __DIR__));
        $this->assertSame('b', $c->s('bar')->requireString('a'));
    }

    /**
     * @return void
     */
    public function testMyConfigDefaultValues()
    {
        $c = new MyConfig(['a' => ['b' => ['c' => 'd']]]);
        $this->assertSame(['baz'], $c->s('foo')->s('bar')->toArray());
        $this->assertSame(['b' => ['c' => 'd']], $c->requireArray('a'));
    }
}
