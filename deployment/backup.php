<?php

// CLI only. Creates a private pre-deployment backup outside public_html.
// Usage from app root: php deployment/backup.php /absolute/private/backup-root
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$root = realpath($argv[1] ?? '');
$application = realpath(base_path());
if (!$root || !is_dir($root) || $root === $application || str_starts_with($root, $application.DIRECTORY_SEPARATOR)) {
    throw new RuntimeException('Backup root must be an existing private directory outside the application.');
}
$previousMask = umask(0077);
$backup = $root.'/release-'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(3));
mkdir($backup, 0700);
try {
    foreach (['.env', '.htaccess', 'index.php', 'build/manifest.json'] as $file) {
        if (is_file(base_path($file))) {
            copy(base_path($file), $backup.'/'.str_replace('/', '-', $file));
        }
    }
    $connection = config('database.default');
    $database = config('database.connections.'.$connection);
    if ($database['driver'] === 'mysql') {
        $quote = static fn ($value) => '"'.str_replace(["\\", '"', "\n", "\r"], ["\\\\", '\\"', '\\n', '\\r'], (string) $value).'"';
        $client = $backup.'/client.cnf';
        file_put_contents($client, "[client]\nuser=".$quote($database['username'])."\npassword=".$quote($database['password'])."\nhost=".$quote($database['host'])."\nport=".(int) ($database['port'] ?? 3306)."\n");
        try {
            $process = proc_open(['mysqldump', '--defaults-extra-file='.$client, '--single-transaction', '--no-tablespaces', '--skip-lock-tables', $database['database']], [
                0 => ['pipe', 'r'], 1 => ['file', $backup.'/database.sql', 'w'], 2 => ['file', $backup.'/database-backup.log', 'w'],
            ], $pipes);
            if (!is_resource($process)) throw new RuntimeException('Database backup could not start.');
            fclose($pipes[0]);
            if (proc_close($process) !== 0 || filesize($backup.'/database.sql') < 100) {
                throw new RuntimeException('Database backup failed; inspect the private backup log before deploying.');
            }
        } finally {
            if (is_file($client)) unlink($client);
        }
    } elseif ($database['driver'] === 'sqlite') {
        DB::connection()->statement("VACUUM INTO '".str_replace("'", "''", $backup.'/database.sqlite')."'");
    } else {
        throw new RuntimeException('Unsupported backup driver; do not deploy without a verified backup.');
    }
    $git = proc_open(['git', 'rev-parse', 'HEAD'], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $gitPipes, base_path());
    if (!is_resource($git)) throw new RuntimeException('Could not read the release revision.');
    fclose($gitPipes[0]);
    $commit = trim(stream_get_contents($gitPipes[1]));
    fclose($gitPipes[1]);
    fclose($gitPipes[2]);
    if (proc_close($git) !== 0 || !preg_match('/^[a-f0-9]{40}$/', $commit)) {
        throw new RuntimeException('Could not verify the release revision.');
    }
    file_put_contents($backup.'/commit.txt', $commit."\n");
    echo 'BACKUP_OK '.$backup.PHP_EOL;
} finally {
    umask($previousMask);
}
