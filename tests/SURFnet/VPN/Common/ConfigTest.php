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
namespace SURFnet\VPN\Common;

require_once sprintf('%s/Test/MyConfig.php', __DIR__);

use SURFnet\VPN\Common\Test\MyConfig;
use PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleConfig()
    {
        $c = new Config(
            [
                'foo' => 'bar',
            ]
        );
        $this->assertSame('bar', $c->v('foo'));
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
        $this->assertSame('baz', $c->v('foo', 'bar'));
    }

    public function testNoParameters()
    {
        $configData = ['foo' => 'bar'];
        $c = new Config($configData);
        $this->assertSame($configData, $c->v());
    }

    public function testExists()
    {
        $c = new Config(['foo' => 'bar']);
        $this->assertTrue($c->e('foo'));
        $this->assertFalse($c->e('bar'));
    }

    /**
     * @expectedException SURFnet\VPN\Common\Exception\ConfigException
     * @expectedExceptionMessage missing configuration field "foo"
     */
    public function testMissingConfig()
    {
        $c = new Config([]);
        $c->v('foo');
    }

    /**
     * @expectedException SURFnet\VPN\Common\Exception\ConfigException
     * @expectedExceptionMessage missing configuration field "foo,baz"
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
        $c->v('foo', 'baz');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage requested configuration field must be string
     */
    public function testNonString()
    {
        $c = new Config([]);
        $c->v(5);
    }

    public function testFromFile()
    {
        $c = Config::fromFile(sprintf('%s/data/config.yml', __DIR__));
        $this->assertSame('b', $c->v('bar', 'a'));
    }

    public function testMyConfigDefaultValues()
    {
        $c = new MyConfig(['a' => 'b']);
        $this->assertSame('bar', $c->v('foo'));
        $this->assertSame('b', $c->v('a'));
    }

    /**
     * @expectedException SURFnet\VPN\Common\Exception\ConfigException
     * @expectedExceptionMessage the value of configuration field "a" does not pass validator "is_string"
     */
    public function testWrongType()
    {
        $c = new MyConfig(['a' => true]);
        $c->s('a');
    }
}
