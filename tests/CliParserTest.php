<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LetsConnect\Common\Tests;

use LetsConnect\Common\CliParser;
use PHPUnit\Framework\TestCase;

class CliParserTest extends TestCase
{
    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
     * @expectedException \LetsConnect\Common\Exception\CliException
     *
     * @expectedExceptionMessage missing required parameter "--instance"
     *
     * @return void
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
     * @expectedException \LetsConnect\Common\Exception\CliException
     *
     * @expectedExceptionMessage missing required parameter value for option "--instance"
     *
     * @return void
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

    /**
     * @return void
     */
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
