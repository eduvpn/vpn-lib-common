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
use SURFnet\VPN\Common\CliParser;

class CliParserTest extends PHPUnit_Framework_TestCase
{
    public function testRequiredArgumentWithValue()
    {
        $p = new CliParser(
            'Test',
            [
                'foo' => ['Foo', true, true],
                'bar' => ['Bar', false, false],
                'baz' => ['Baz', false, true],
                'xyz' => ['Xyz', true, false],
            ]
        );

        $config = $p->parse(['name_of_program', '--foo', 'foo', '--baz', 'baz']);
        $this->assertSame(file_get_contents(sprintf('%s/data/help.txt', __DIR__)), $p->help());
        $this->assertSame('foo', $config->getItem('foo'));
    }

    public function testTwoRequiredArgumentsWithValues()
    {
        $p = new CliParser(
            'Test',
            [
                'instance' => ['instance identifier', true, true],
                'generate' => ['generate a new certificate', true, true],
            ]
        );
        $config = $p->parse(['name_of_program', '--instance', 'vpn.example', '--generate', 'vpn00.example']);
        $this->assertSame('vpn.example', $config->getItem('instance'));
        $this->assertSame('vpn00.example', $config->getItem('generate'));
    }

    public function testRequiredArgument()
    {
        $p = new CliParser(
            'Test',
            [
                'install' => ['install the firewall', false, false],
            ]
        );
        $config = $p->parse(['name_of_program', '--install']);
        $this->assertSame([], $config->getItem('install'));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Exception\CliException
     * @expectedExceptionMessage missing required parameter "--instance"
     */
    public function testMissingRequiredArgument()
    {
        $p = new CliParser(
            'Test',
            [
                'instance' => ['instance identifier', true, true],
            ]
        );
        $p->parse(['name_of_program']);
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Exception\CliException
     * @expectedExceptionMessage missing required parameter value for option "--instance"
     */
    public function testMissingRequiredArgumentValue()
    {
        $p = new CliParser(
            'Test',
            [
                'instance' => ['instance identifier', true, true],
            ]
        );
        $p->parse(['name_of_program', '--instance']);
    }

    public function testHelpCall()
    {
        $p = new CliParser(
            'Test',
            [
                'instance' => ['instance identifier', true, true],
            ]
        );
        $config = $p->parse(['name_of_program', '--help']);
        $this->assertSame(
            [
                'help' => true,
            ],
            $config->toArray()
        );
    }
}
