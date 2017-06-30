<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common\Http;

use SURFnet\VPN\Common\Http\Exception\HttpException;
use SURFnet\VPN\Common\Http\Exception\InputValidationException;
use SURFnet\VPN\Common\TplInterface;

class Service
{
    /** @var \SURFnet\VPN\Common\TplInterface|null */
    private $tpl;

    /** @var array */
    private $routes;

    /** @var array */
    private $beforeHooks;

    /** @var array */
    private $afterHooks;

    public function __construct(TplInterface $tpl = null)
    {
        $this->tpl = $tpl;
        $this->routes = [
            'GET' => [],
            'POST' => [],
        ];
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
                $hookResponse = $v->executeBefore($request, $hookData);
                // if we get back a Response object, return it immediately
                if ($hookResponse instanceof Response) {
                    // run afterHooks
                    return $this->runAfterHooks($request, $hookResponse);
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
            return $this->runAfterHooks($request, $response);
        } catch (InputValidationException $e) {
            // in case an InputValidationException is encountered, convert it
            // into a Bad Request HTTP response
            throw new HttpException($e->getMessage(), 400);
        } catch (HttpException $e) {
            if ($request->isBrowser()) {
                if (is_null($this->tpl)) {
                    // template not available
                    $response = new Response($e->getCode(), 'text/plain');
                    $response->setBody(sprintf('%d: %s', $e->getCode(), $e->getMessage()));
                } else {
                    // template available
                    $response = new Response($e->getCode(), 'text/html');
                    $response->setBody(
                        $this->tpl->render(
                            'errorPage',
                            [
                                'code' => $e->getCode(),
                                'message' => $e->getMessage(),
                            ]
                        )
                    );
                }
            } else {
                // not a browser
                $response = new Response($e->getCode(), 'application/json');
                $response->setBody(json_encode(['error' => $e->getMessage()]));
            }

            foreach ($e->getResponseHeaders() as $key => $value) {
                $response->addHeader($key, $value);
            }

            // after hooks
            return $this->runAfterHooks($request, $response);
        }
    }

    private function runAfterHooks(Request $request, Response $response)
    {
        foreach ($this->afterHooks as $v) {
            $response = $v->executeAfter($request, $response);
        }

        return $response;
    }
}
