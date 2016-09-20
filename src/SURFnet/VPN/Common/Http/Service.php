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

use SURFnet\VPN\Common\Http\Exception\HttpException;

class Service
{
    /** @var array */
    private $routes;

    /** @var array */
    private $beforeHooks;

    /** @var array */
    private $afterHooks;

    public function __construct()
    {
        $this->routes = [];
        $this->beforeHooks = [];
        $this->afterHooks = [];
    }

    public function addBeforeHook($name, BeforeHookInterface $beforeHook)
    {
        $this->beforeHooks[$name] = $beforeHook;
    }

    public function addAfterHook($name, AfterHookInterface $afterHook)
    {
        $this->afterHooks[$name] = $afterHook;
    }

    public function addRoute($requestMethod, $pathInfo, callable $callback)
    {
        $this->routes[$requestMethod][$pathInfo] = $callback;
    }

    public function get($pathInfo, callable $callback)
    {
        $this->addRoute('GET', $pathInfo, $callback);
    }

    public function post($pathInfo, callable $callback)
    {
        $this->addRoute('POST', $pathInfo, $callback);
    }

    public function addModule(ServiceModuleInterface $module)
    {
        $module->init($this);
    }

    public function run(Request $request)
    {
        try {
            // before hooks
            $hookData = [];
            foreach ($this->beforeHooks as $k => $v) {
                $hookResponse = $v->executeBefore($request);
                // if we get back a Response object, return it immediately
                if ($hookResponse instanceof Response) {
                    // run afterHooks
                    foreach ($this->afterHooks as $k => $v) {
                        $hookResponse = $v->executeAfter($request, $hookResponse);
                    }

                    return $hookResponse;
                }

                $hookData[$k] = $hookResponse;
            }

            $requestMethod = $request->getRequestMethod();
            $pathInfo = $request->getPathInfo();

            if (!array_key_exists($requestMethod, $this->routes)) {
                throw new HttpException(
                    sprintf('method "%s" not allowed', $requestMethod),
                    405,
                    ['Allow' => implode(',', array_keys($this->routes))]
                );
            }
            if (!array_key_exists($pathInfo, $this->routes[$requestMethod])) {
                throw new HttpException(
                    sprintf('"%s" not found', $pathInfo),
                    404
                );
            }

            $response = $this->routes[$requestMethod][$pathInfo]($request, $hookData);

            // after hooks
            foreach ($this->afterHooks as $k => $v) {
                $response = $v->executeAfter($request, $response);
            }

            return $response;
        } catch (HttpException $e) {
            $response = new Response($e->getCode(), 'application/json');
            foreach ($e->getResponseHeaders() as $key => $value) {
                $response->addHeader($key, $value);
            }
            $response->setBody(json_encode(['error' => $e->getMessage()]));

            return $response;
        }
    }
}
