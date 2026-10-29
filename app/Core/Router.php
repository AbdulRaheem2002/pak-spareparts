<?php
/**
 * PakAutoParts - Lightweight Clean URL Router
 * Supports parameter matching (/parts/{make}/{model}/{year}), middleware chains, and REST dispatching.
 */

declare(strict_types=1);

namespace App\Core;

class Router
{
    private static array $routes = [];
    private static array $namedRoutes = [];
    private static array $middlewareAliases = [
        'auth'  => \App\Core\Auth::class,
        'csrf'  => \App\Core\Csrf::class,
    ];

    public static function get(string $pattern, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        self::addRoute('GET', $pattern, $handler, $middlewares, $name);
    }

    public static function post(string $pattern, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        self::addRoute('POST', $pattern, $handler, $middlewares, $name);
    }

    public static function any(string $pattern, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        self::addRoute('ANY', $pattern, $handler, $middlewares, $name);
    }

    private static function addRoute(string $method, string $pattern, array|callable $handler, array $middlewares = [], ?string $name = null): void
    {
        $regex = self::compilePattern($pattern);
        $route = [
            'method'      => $method,
            'pattern'     => $pattern,
            'regex'       => $regex,
            'handler'     => $handler,
            'middlewares' => $middlewares,
            'name'        => $name,
        ];

        self::$routes[] = $route;

        if ($name) {
            self::$namedRoutes[$name] = $pattern;
        }
    }

    private static function compilePattern(string $pattern): string
    {
        $pattern = trim($pattern, '/');
        if ($pattern === '') {
            return '#^/?$#';
        }

        // Convert {param} or {param:regex} into named regex groups
        $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)(?::([^\}]+))?\}/', function ($matches) {
            $name = $matches[1];
            $customRegex = $matches[2] ?? '[^/]+';
            return '(?P<' . $name . '>' . $customRegex . ')';
        }, $pattern);

        return '#^/' . $pattern . '/?$#';
    }

    public static function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach (self::$routes as $route) {
            if ($route['method'] !== 'ANY' && $route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($value);
                    }
                }

                // Execute middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $response = self::handleMiddleware($middleware, $request);
                    if ($response instanceof Response) {
                        return $response;
                    }
                }

                return self::executeHandler($route['handler'], $request, $params);
            }
        }

        // Check if there's a 301 URL redirect in DB (SEO slug migration)
        $redirect = self::checkUrlRedirect($uri);
        if ($redirect) {
            return Response::redirect($redirect['new_url'], 301);
        }

        // 404 Fallback
        return self::handleNotFound($request);
    }

    private static function handleMiddleware(string $middleware, Request $request): ?Response
    {
        if ($middleware === 'auth') {
            if (!Auth::check()) {
                if ($request->isAjax()) {
                    return Response::json(['error' => 'Unauthorized access'], 401);
                }
                Session::flash('error', 'Please log in to access this area.');
                return Response::redirect('/admin/login');
            }
        }

        if (str_starts_with($middleware, 'role:')) {
            $requiredRole = substr($middleware, 5);
            if (!Auth::hasRole($requiredRole)) {
                if ($request->isAjax()) {
                    return Response::json(['error' => 'Insufficient permissions'], 403);
                }
                Session::flash('error', 'Access forbidden: Insufficient permissions.');
                return Response::redirect('/admin');
            }
        }

        if ($middleware === 'csrf') {
            if ($request->isPost() && !Csrf::validate()) {
                if ($request->isAjax()) {
                    return Response::json(['error' => 'CSRF verification failed. Please refresh the page.'], 403);
                }
                Session::flash('error', 'Form session expired. Please submit again.');
                return Response::redirect($request->uri());
            }
        }

        return null;
    }

    private static function executeHandler(array|callable $handler, Request $request, array $params): Response
    {
        if (is_callable($handler)) {
            $result = call_user_func_array($handler, array_merge([$request], $params));
        } elseif (is_array($handler)) {
            [$controllerClass, $action] = $handler;
            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller not found: {$controllerClass}");
            }
            $controller = new $controllerClass();
            if (!method_exists($controller, $action)) {
                throw new \RuntimeException("Action '{$action}' not found in controller {$controllerClass}");
            }
            $result = call_user_func_array([$controller, $action], array_merge([$request], $params));
        } else {
            throw new \RuntimeException("Invalid route handler format");
        }

        if ($result instanceof Response) {
            return $result;
        }

        if (is_string($result)) {
            return Response::html($result);
        }

        if (is_array($result)) {
            return Response::json($result);
        }

        return new Response();
    }

    private static function checkUrlRedirect(string $uri): ?array
    {
        try {
            $cleanUri = trim($uri, '/');
            $redirect = Database::selectOne("SELECT new_slug FROM url_redirects WHERE old_slug = :old LIMIT 1", ['old' => $cleanUri]);
            if ($redirect) {
                return ['new_url' => '/' . ltrim($redirect['new_slug'], '/')];
            }
        } catch (\Throwable $e) {
            // Table might not exist yet during initial setup
        }
        return null;
    }

    private static function handleNotFound(Request $request): Response
    {
        if ($request->isAjax()) {
            return Response::json(['error' => 'Endpoint not found', 'code' => 404], 404);
        }

        $html = View::render('layouts/error', [
            'code'        => 404,
            'title'       => 'Page Not Found | PakAutoParts',
            'heading'     => 'Part or Page Not Found',
            'message'     => 'The spare part or page you were looking for doesn\'t exist or has moved.',
            'query'       => $request->get('q', ''),
        ]);

        return Response::html($html, 404);
    }
}

