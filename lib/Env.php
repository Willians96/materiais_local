<?php

/**
 * Carrega variáveis do .env pra $_ENV
 * Formato do .env: CHAVE=valor (linhas iniciadas com # são comentários)
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, " \t\"'");
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

function env(string $key, $default = null): ?string
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}
