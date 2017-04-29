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

namespace SURFnet\VPN\Common\Tests\Http;

use PHPUnit_Framework_TestCase;
use SURFnet\VPN\Common\Http\CallbackHook;
use SURFnet\VPN\Common\Http\Request;
use SURFnet\VPN\Common\Http\Response;
use SURFnet\VPN\Common\Http\Service;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $request = new TestRequest(
            [
                'REQUEST_URI' => '/foo',
            ]
        );

        $service = new Service();
        $service->get('/foo', function (Request $request) {
            $response = new Response(201, 'application/json');
            $response->setBody('{}');

            return $response;
        });
        $service->post('/bar', function (Request $request) {
            return new Response();
        });
        $response = $service->run($request);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('{}', $response->getBody());
    }

    public function testMissingDocument()
    {
        $request = new TestRequest(
            [
                'REQUEST_URI' => '/bar',
            ]
        );

        $service = new Service();
        $service->get('/foo', function (Request $request) {
            $response = new Response(201, 'application/json');
            $response->setBody('{}');

            return $response;
        });
        $response = $service->run($request);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('{"error":"\"\/bar\" not found"}', $response->getBody());
    }

    public function testUnsupportedMethod()
    {
        $request = new TestRequest(
            [
                'REQUEST_METHOD' => 'DELETE',
                'REQUEST_URI' => '/bar',
            ]
        );

        $service = new Service();
        $service->get('/foo', function (Request $request) {
            return new Response();
        });
        $service->post('/foo', function (Request $request) {
            return new Response();
        });
        $response = $service->run($request);

        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame('GET,POST', $response->getHeader('Allow'));
        $this->assertSame('{"error":"method \"DELETE\" not allowed"}', $response->getBody());
    }

    public function testHooks()
    {
        $request = new TestRequest(
            [
                'REQUEST_URI' => '/foo',
            ]
        );

        $service = new Service();
        $callbackHook = new CallbackHook(
            function (Request $request) {
                return '12345';
            },
            function (Request $request, Response $response) {
                $response->addHeader('Foo', 'Bar');

                return $response;
            }
        );
        $service->addBeforeHook('test', $callbackHook);
        $service->addAfterHook('test', $callbackHook);

        $service->get('/foo', function (Request $request, array $hookData) {
            $response = new Response();
            $response->setBody($hookData['test']);

            return $response;
        });
        $response = $service->run($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('12345', $response->getBody());
        $this->assertSame('Bar', $response->getHeader('Foo'));
    }

    public function testHookResponse()
    {
        $request = new TestRequest(
            [
                'REQUEST_URI' => '/foo',
            ]
        );
        $service = new Service();
        $callbackHook = new CallbackHook(
            function (Request $request) {
                return new Response(201);
            }
        );
        $service->addBeforeHook('test', $callbackHook);

        $service->get('/foo', function (Request $request, array $hookData) {
            return new Response();
        });
        $response = $service->run($request);

        $this->assertSame(201, $response->getStatusCode());
    }

    public function testHookDataPassing()
    {
        $request = new TestRequest([]);
        $service = new Service();
        $service->addBeforeHook(
            'test',
            new CallbackHook(
                function (Request $request, array $hookData) {
                    // this should be available in the next before hook
                    return '12345';
                }
            )
        );
        $service->addBeforeHook(
            'test2',
            new CallbackHook(
                function (Request $request, array $hookData) {
                    $response = new Response();
                    $response->setBody($hookData['test']);

                    return $response;
                }
            )
        );
        $response = $service->run($request);
        $this->assertSame('12345', $response->getBody());
    }

    public function testBrowserNotFoundWithoutTpl()
    {
        $request = new TestRequest(
            [
                'REQUEST_URI' => '/bar',
                'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $service = new Service();
        $service->get('/foo', function (Request $request) {
            return new Response(200);
        });
        $response = $service->run($request);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('404: "/bar" not found', $response->getBody());
    }

    public function testBrowserNotFoundWithTpl()
    {
        $request = new TestRequest(
            [
                'REQUEST_URI' => '/bar',
                'HTTP_ACCEPT' => 'text/html',
            ]
        );

        $service = new Service(new TestHtmlTpl());
        $service->get('/foo', function (Request $request) {
            return new Response(200);
        });
        $response = $service->run($request);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('<html><head><title>404</title></head><body><h1>Error (404)</h1><p>"/bar" not found</p></body></html>', $response->getBody());
    }
}
