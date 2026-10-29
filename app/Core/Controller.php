<?php
/**
 * PakAutoParts - Base Controller
 * Provides standardized response helpers, input validation, and security utilities.
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $template, array $data = [], ?string $layout = 'layouts/main'): Response
    {
        $html = View::render($template, $data, $layout);
        return Response::html($html);
    }

    protected function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }

    protected function back(): Response
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return Response::redirect($referer);
    }

    protected function validateCsrf(): bool
    {
        if (!Csrf::validate()) {
            Session::flash('error', 'Security token expired. Please retry.');
            return false;
        }
        return true;
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $val = $data[$field] ?? null;

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && ($val === null || trim((string)$val) === '')) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                    break;
                }

                if ($val !== null && trim((string)$val) !== '') {
                    if ($rule === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = 'Please enter a valid email address.';
                    } elseif ($rule === 'numeric' && !is_numeric($val)) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number.';
                    } elseif (str_starts_with($rule, 'min:')) {
                        $min = (int)substr($rule, 4);
                        if (strlen((string)$val) < $min) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.";
                        }
                    } elseif (str_starts_with($rule, 'max:')) {
                        $max = (int)substr($rule, 4);
                        if (strlen((string)$val) > $max) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " may not exceed {$max} characters.";
                        }
                    }
                }
            }
        }

        return $errors;
    }
}

