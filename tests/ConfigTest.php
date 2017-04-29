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
