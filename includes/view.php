<?php

declare(strict_types=1);

function template_path(string $template): string
{
    $path = MIS_ROOT . '/templates/' . ltrim($template, '/');
    if (!is_file($path)) {
        throw new RuntimeException('Template not found: ' . $template);
    }

    return $path;
}

function render(string $template, array $vars = [], ?string $layout = null): void
{
    $flash = flash_get();

    if ($layout === null) {
        extract($vars, EXTR_SKIP);
        require template_path($template);
        return;
    }

    $__template = $template;
    $__vars = $vars;
    require template_path($layout);
}
