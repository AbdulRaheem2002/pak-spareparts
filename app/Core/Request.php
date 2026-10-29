<?php
/**
 * PakAutoParts - HTTP Request Abstraction & Sanitizer
 */

declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private ?array $json = null;

    public function __construct(
        ?array $get = null,
        ?array $post = null,
        ?array $files = null,
        ?array $server = null
    ) {
        $this->get = $get ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->files = $files ?? $_FILES;
        $this->server = $server ?? $_SERVER;
    }

    public static function capture(): self
    {
        return new self();
    }

    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST') {
            $override = $this->post['_method'] ?? $this->server['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? null;
            if ($override && in_array(strtoupper($override), ['PUT', 'PATCH', 'DELETE'], true)) {
                return strtoupper($override);
            }
        }
        return $method;
    }

    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $pos = strpos($uri, '?');
        if ($pos !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Strip subfolder if app runs inside a subdirectory (e.g. /sparepart-website)
        $scriptName = $this->server['SCRIPT_NAME'] ?? '';
        $baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if (!empty($baseDir) && $baseDir !== '/' && str_starts_with($uri, $baseDir)) {
            $uri = substr($uri, strlen($baseDir));
        }

        $uri = '/' . trim($uri, '/');
        return $uri === '//' ? '/' : $uri;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($this->json === null) {
            $content = file_get_contents('php://input');
            $decoded = json_decode($content, true);
            $this->json = is_array($decoded) ? $decoded : [];
        }

        if ($key === null) {
            return $this->json;
        }

        return $this->json[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $val = $this->post($key) ?? $this->get($key) ?? $this->json($key);
        return $val ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->json() ?? []);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->get[$key]) || isset($this->post[$key]) || isset($this->json()[$key]);
    }

    public function filled(string $key): bool
    {
        $val = $this->input($key);
        return $val !== null && trim((string)$val) !== '';
    }

    public function isAjax(): bool
    {
        return (isset($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($this->server['HTTP_ACCEPT']) && str_contains($this->server['HTTP_ACCEPT'], 'application/json'));
    }

    public function ip(): string
    {
        $keys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($keys as $key) {
            if (!empty($this->server[$key])) {
                $ips = explode(',', (string)$this->server[$key]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '127.0.0.1';
    }

    public function userAgent(): string
    {
        return (string)($this->server['HTTP_USER_AGENT'] ?? 'Unknown');
    }
}

