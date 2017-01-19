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

namespace SURFnet\VPN\Common\Http;

require_once sprintf('%s/Test/TestRequest.php', __DIR__);

use PHPUnit_Framework_TestCase;
use SURFnet\VPN\Common\Http\Test\TestRequest;

class CsrfProtectionHookTest extends PHPUnit_Framework_TestCase
{
    public function testGoodPostReferrer()
    {
        $request = new TestRequest(
            [
                'HTTP_ACCEPT' => 'text/html',
                'REQUEST_METHOD' => 'POST',
                'HTTP_REFERER' => 'http://vpn.example/foo',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $this->assertTrue($referrerCheckHook->executeBefore($request, []));
    }

    public function testGoodPostOrigin()
    {
        $request = new TestRequest(
            [
                'HTTP_ACCEPT' => 'text/html',
                'REQUEST_METHOD' => 'POST',
                'HTTP_ORIGIN' => 'http://vpn.example',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $this->assertTrue($referrerCheckHook->executeBefore($request, []));
    }

    public function testGet()
    {
        $request = new TestRequest(
            [
                'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $this->assertFalse($referrerCheckHook->executeBefore($request, []));
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage CSRF protection failed, no HTTP_ORIGIN or HTTP_REFERER
     */
    public function testCheckPostNoReferrer()
    {
        $request = new TestRequest(
            [
                'REQUEST_METHOD' => 'POST',
                'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $referrerCheckHook->executeBefore($request, []);
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage CSRF protection failed: unexpected HTTP_REFERER
     */
    public function testCheckPostWrongReferrer()
    {
        $request = new TestRequest(
            [
            'REQUEST_METHOD' => 'POST',
            'HTTP_REFERER' => 'http://www.attacker.org/foo',
            'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $referrerCheckHook->executeBefore($request, []);
    }

    /**
     * @expectedException \SURFnet\VPN\Common\Http\Exception\HttpException
     * @expectedExceptionMessage CSRF protection failed: unexpected HTTP_ORIGIN
     */
    public function testCheckPostWrongOrigin()
    {
        $request = new TestRequest(
            [
            'REQUEST_METHOD' => 'POST',
            'HTTP_ORIGIN' => 'http://www.attacker.org',
            'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $referrerCheckHook->executeBefore($request, []);
    }

    public function testNonBrowser()
    {
        $request = new TestRequest(
            [
                'REQUEST_METHOD' => 'POST',
                'HTTP_ACCEPT' => 'application/json',
            ]
        );

        $referrerCheckHook = new CsrfProtectionHook();
        $referrerCheckHook->executeBefore($request, []);
    }
}
