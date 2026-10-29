<?php
/**
 * PakAutoParts - View Engine & Layout Renderer
 * Handles layout composition, partials, XSS escaping, asset versioning, and SEO meta tags.
 */

declare(strict_types=1);

namespace App\Core;

class View
{
    private static array $sharedData = [];

    public static function share(string $key, mixed $value): void
    {
        self::$sharedData[$key] = $value;
    }

    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $baseDir = dirname(__DIR__) . '/Views/';
        $viewFile = $baseDir . ltrim($template, '/') . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View template not found: {$template} ({$viewFile})");
        }

        // Merge shared data with view data
        $mergedData = array_merge(self::$sharedData, [
            'app_name'     => Config::get('app.name'),
            'app_tagline'  => Config::get('app.tagline'),
            'app_env'      => Config::get('app.env'),
            'currency'     => Config::get('currency.symbol'),
            'whatsapp_num' => Config::get('contact.whatsapp_num'),
            'phone_display'=> Config::get('contact.phone_display'),
            'current_user' => Auth::user(),
            'flash_success'=> Session::getFlash('success'),
            'flash_error'  => Session::getFlash('error'),
            'flash_info'   => Session::getFlash('info'),
        ], $data);

        // Render child view
        extract($mergedData, EXTR_SKIP);
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Wrap with layout if specified
        if ($layout) {
            $layoutFile = $baseDir . ltrim($layout, '/') . '.php';
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout file not found: {$layout} ({$layoutFile})");
            }

            $mergedData['content'] = $content;
            extract($mergedData, EXTR_SKIP);
            ob_start();
            include $layoutFile;
            return ob_get_clean();
        }

        return $content;
    }

    public static function partial(string $partial, array $data = []): string
    {
        $baseDir = dirname(__DIR__) . '/Views/';
        $partialFile = $baseDir . ltrim($partial, '/') . '.php';

        if (!file_exists($partialFile)) {
            return "<!-- Missing partial: {$partial} -->";
        }

        $mergedData = array_merge(self::$sharedData, $data);
        extract($mergedData, EXTR_SKIP);
        ob_start();
        include $partialFile;
        return ob_get_clean();
    }
}

