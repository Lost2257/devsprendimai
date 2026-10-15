<?php

declare(strict_types=1);

// CLI-only deployment helpers. No secret should ever be included in errors.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

function hostingerMailEnvironment(string $original, #[SensitiveParameter] string $password): string
{
    if ($password === '' || strlen($password) > 1024 || preg_match('/[\x00-\x1f\x7f]/', $password)) {
        throw new RuntimeException('Invalid mailbox password input.');
    }

    $values = [
        'MAIL_MAILER' => 'smtp',
        'MAIL_SCHEME' => 'smtps',
        'MAIL_URL' => '',
        'MAIL_HOST' => 'smtp.hostinger.com',
        'MAIL_PORT' => '465',
        'MAIL_USERNAME' => 'info@devsprendimai.lt',
        // Preserve reserved literals and quote-wrapped passwords through Laravel
        // Env::get(), which otherwise coerces null/false or strips one quote pair.
        'MAIL_PASSWORD' => '"'.$password.'"',
        'MAIL_FROM_ADDRESS' => 'info@devsprendimai.lt',
        'MAIL_FROM_NAME' => 'DevSprendimai',
    ];
    try {
        $before = Dotenv\Dotenv::parse($original);
        $newline = str_contains($original, "\r\n") ? "\r\n" : "\n";
        $lines = preg_split('/\r\n|\n|\r/', $original);
        $lines = array_filter($lines, static function ($line) use ($values) {
            return !preg_match('/^\s*(?:export\s+)?([A-Z_]+)\s*=/', $line, $match)
                || !array_key_exists($match[1], $values);
        });
        $result = rtrim(implode($newline, $lines), "\r\n").$newline;
        foreach ($values as $key => $value) {
            $escaped = str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $value);
            $result .= $key.'="'.$escaped.'"'.$newline;
        }
        $after = Dotenv\Dotenv::parse($result);
        if (array_diff_key($before, $values) !== array_diff_key($after, $values)) {
            throw new RuntimeException('Unrelated configuration would change.');
        }
        foreach ($values as $key => $value) {
            if (($after[$key] ?? null) !== $value) {
                throw new RuntimeException('Mail configuration could not be verified.');
            }
        }
        return $result;
    } catch (Throwable) {
        // Dotenv parser errors can contain raw values. Never rethrow their text.
        throw new RuntimeException('Environment configuration validation failed.');
    }
}

function writePrivateMailFile(string $path, #[SensitiveParameter] string $contents): void
{
    $temporary = tempnam(dirname($path), '.mail-setup-');
    if ($temporary === false) {
        throw new RuntimeException('Could not create a private configuration file.');
    }
    try {
        if (!chmod($temporary, 0600) || file_put_contents($temporary, $contents) !== strlen($contents)
            || !rename($temporary, $path)) {
            throw new RuntimeException('Could not save configuration.');
        }
    } finally {
        if (is_file($temporary)) {
            unlink($temporary);
        }
    }
}
