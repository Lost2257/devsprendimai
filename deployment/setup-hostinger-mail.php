<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
// Do not allow warnings, trace arguments or SMTP transcripts to expose secrets.
ini_set('display_errors', '0');
ini_set('zend.exception_ignore_args', '1');
error_reporting(0);
umask(0077);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/mail-environment.php';

if (($argv[1] ?? '') !== '--password-stdin') {
    fwrite(STDERR, "Paleiskite: bash deployment/setup-hostinger-mail.sh\n");
    exit(1);
}

$phase = 'prepare';
$changed = false;
$transport = null;
try {
    $root = realpath(__DIR__.'/..');
    // This helper is deliberately scoped to the owner's production deployment.
    if ($root !== '/home/u262161004/domains/devsprendimai.lt/public_html') {
        throw new RuntimeException('Wrong deployment directory.');
    }
    $environment = $root.'/.env';
    $cache = $root.'/bootstrap/cache/config.php';
    if (!is_file($environment) || is_link($environment) || is_link($cache)) {
        throw new RuntimeException('Unsafe configuration paths.');
    }
    $original = file_get_contents($environment);
    $oldCache = is_file($cache) ? file_get_contents($cache) : null;
    if ($original === false || $oldCache === false) {
        throw new RuntimeException('Could not read configuration.');
    }
    $parsed = Dotenv\Dotenv::parse($original);
    if (!empty($parsed['APP_CONFIG_CACHE']) || getenv('APP_CONFIG_CACHE')) {
        throw new RuntimeException('Custom configuration cache requires manual setup.');
    }
    $password = stream_get_contents(STDIN, 1025);
    $updated = hostingerMailEnvironment($original, $password);

    $phase = 'authenticate';
    $transport = new Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport('smtp.hostinger.com', 465, true);
    $transport->getStream()->setTimeout(15);
    $transport->setUsername('info@devsprendimai.lt');
    $transport->setPassword($password);
    $transport->start(); // TLS + AUTH only: no test email or enquiry is sent.
    if (!array_key_exists('AUTH', $transport->getCapabilities())) {
        throw new RuntimeException('SMTP server did not offer authentication.');
    }
    $transport->stop();
    $transport = null;
    unset($password);

    $phase = 'save';
    $backupRoot = '/home/u262161004/deploy-backups';
    if (!is_dir($backupRoot) || is_link($backupRoot)) {
        throw new RuntimeException('Private backup directory is unavailable.');
    }
    $backup = $backupRoot.'/mail-'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(3));
    if (!mkdir($backup, 0700)) {
        throw new RuntimeException('Could not back up configuration.');
    }
    writePrivateMailFile($backup.'/.env', $original);
    if ($oldCache !== null) {
        writePrivateMailFile($backup.'/config.php', $oldCache);
    }
    // Refuse to overwrite a concurrent configuration change.
    if (file_get_contents($environment) !== $original
        || (is_file($cache) ? file_get_contents($cache) : null) !== $oldCache) {
        throw new RuntimeException('Configuration changed during setup.');
    }
    writePrivateMailFile($environment, $updated);
    $changed = true;
    $process = proc_open([PHP_BINARY, 'artisan', 'config:cache', '--no-interaction'], [
        0 => ['file', '/dev/null', 'r'],
        1 => ['file', '/dev/null', 'w'],
        2 => ['file', '/dev/null', 'w'],
    ], $pipes, $root);
    if (!is_resource($process) || proc_close($process) !== 0) {
        throw new RuntimeException('Configuration cache failed.');
    }
    $config = require $cache;
    $mail = $config['mail'] ?? [];
    $expected = Dotenv\Dotenv::parse($updated);
    if (($mail['default'] ?? '') !== 'smtp'
        || ($mail['mailers']['smtp']['scheme'] ?? '') !== 'smtps'
        || ($mail['mailers']['smtp']['host'] ?? '') !== 'smtp.hostinger.com'
        || (int) ($mail['mailers']['smtp']['port'] ?? 0) !== 465
        || ($mail['mailers']['smtp']['username'] ?? '') !== 'info@devsprendimai.lt'
        || ($mail['mailers']['smtp']['password'] ?? '') !== substr($expected['MAIL_PASSWORD'], 1, -1)
        || !empty($mail['mailers']['smtp']['url'])
        || ($mail['from']['address'] ?? '') !== 'info@devsprendimai.lt') {
        throw new RuntimeException('Active mail configuration could not be verified.');
    }
    if (!chmod($cache, 0600)) {
        throw new RuntimeException('Could not protect configuration cache.');
    }
    echo "PASTAS PRIJUNGTAS. Saugus SMTP prisijungimas patikrintas.\n";
    echo "Naujos uzklausos bus issaugomos dashboard ir siunciamos el. pastu.\n";
    echo "Bandomasis laiskas dar nesiustas; gavima dezuteje reikia patikrinti atskirai.\n";
} catch (Throwable) {
    $rolledBack = !$changed;
    if ($changed) {
        try {
            writePrivateMailFile($environment, $original);
            if ($oldCache !== null) {
                writePrivateMailFile($cache, $oldCache);
            } elseif (is_file($cache) && !unlink($cache)) {
                throw new RuntimeException('Could not restore cache.');
            }
            $rolledBack = true;
        } catch (Throwable) {
            // Never print the original exception: it may include credentials.
        }
    }
    fwrite(STDERR, $phase === 'authenticate'
        ? "PASTAS NEPRIJUNGTAS. Patikrinkite dezutes slaptazodi arba SMTP prieinamuma.\n"
        : "PASTO NUSTATYMAS NEPAVYKO. Reikia patikrinti serverio konfiguracija.\n");
    fwrite(STDERR, $rolledBack
        ? "Ankstesne svetaines konfiguracija nepakeista arba atkurta.\n"
        : "SVARBU: konfiguracijos atkurti nepavyko; reikia atkurti privacia atsargine kopija.\n");
    exit(1);
}
