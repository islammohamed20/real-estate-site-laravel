<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Dump the application database (mysqldump → gzip) into the backup directory
 * and prune old copies. Runs automatically every night and on demand from
 * the Maintenance & Backups dashboard page.
 */
class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--keep=30 : Number of backups to keep} {--full : Include uploaded files in a full-site archive}';

    protected $description = 'Create a compressed MySQL backup and prune old ones';

    public function handle(): int
    {
        $connection = config('database.default') ?: 'mysql';
        $config = config("database.connections.{$connection}");

        if (($config['driver'] ?? null) !== 'mysql') {
            $this->error('Only the MySQL connection is supported for backups.');

            return self::FAILURE;
        }

        $dir = $this->backupDirectory();
        $this->ensureDirectory($dir);

        if ($this->option('full')) {
            return $this->createFullBackup($config, $dir);
        }

        $filename = now()->format('Y-m-d_H-i-s').'.sql.gz';
        $path = rtrim($dir, '/').'/'.$filename;

        $credentials = $this->createCredentialsFile($config);

        try {
            $command = sprintf(
                'set -o pipefail; mysqldump --defaults-file=%s --single-transaction --routines --events %s | gzip > %s',
                escapeshellarg($credentials),
                escapeshellarg((string) $config['database']),
                escapeshellarg($path),
            );

            exec('bash -c '.escapeshellarg($command).' 2>&1', $output, $exitCode);
        } finally {
            @unlink($credentials);
        }

        if ($exitCode !== 0 || ! is_file($path) || filesize($path) === 0) {
            @unlink($path);
            $this->error('Backup failed: '.implode("\n", $output));

            return self::FAILURE;
        }

        $this->secureBackupFile($path, $dir);

        $size = $this->humanSize((int) filesize($path));
        $this->info("Database backup created: {$filename} ({$size}).");

        $pruned = $this->prune($dir, (int) $this->option('keep'));

        if ($pruned > 0) {
            $this->info("Pruned {$pruned} old backup(s).");
        }

        return self::SUCCESS;
    }

    /**
     * Directory where backups are stored (configurable via BACKUP_PATH env).
     */
    public function backupDirectory(): string
    {
        $path = env('BACKUP_PATH', storage_path('app/backups'));

        return rtrim((string) $path, '/');
    }

    private function createFullBackup(array $config, string $dir): int
    {
        $stamp = now()->format('Y-m-d_H-i-s');
        $sqlName = '.full-'.$stamp.'.sql';
        $sqlPath = rtrim($dir, '/').'/'.$sqlName;
        $archiveName = $stamp.'.full.tar.gz';
        $archivePath = rtrim($dir, '/').'/'.$archiveName;

        $credentials = $this->createCredentialsFile($config);

        try {
            $dumpCommand = sprintf(
                'mysqldump --defaults-file=%s --single-transaction --routines --events %s > %s',
                escapeshellarg($credentials),
                escapeshellarg((string) $config['database']),
                escapeshellarg($sqlPath),
            );

            exec($dumpCommand.' 2>&1', $output, $exitCode);
        } finally {
            @unlink($credentials);
        }

        if ($exitCode !== 0 || ! is_file($sqlPath) || filesize($sqlPath) === 0) {
            @unlink($sqlPath);
            $this->error('Full backup database dump failed: '.implode("\n", $output));

            return self::FAILURE;
        }

        $projectRoot = base_path();
        $tarCommand = sprintf(
            'tar -czf %s -C %s %s -C %s %s',
            escapeshellarg($archivePath),
            escapeshellarg($dir),
            escapeshellarg($sqlName),
            escapeshellarg($projectRoot),
            escapeshellarg('storage/app/public'),
        );

        exec($tarCommand.' 2>&1', $output, $exitCode);
        @unlink($sqlPath);

        if ($exitCode !== 0 || ! is_file($archivePath) || filesize($archivePath) === 0) {
            @unlink($archivePath);
            $this->error('Full backup archive failed: '.implode("\n", $output));

            return self::FAILURE;
        }

        $this->secureBackupFile($archivePath, $dir);

        $size = $this->humanSize((int) filesize($archivePath));
        $this->info("Full backup created: {$archiveName} ({$size}) — database and uploaded files.");

        $pruned = $this->prune($dir, (int) $this->option('keep'));
        if ($pruned > 0) {
            $this->info("Pruned {$pruned} old backup(s).");
        }

        return self::SUCCESS;
    }
    private function ensureDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (! is_writable($dir)) {
            chmod($dir, 0775);
        }
    }

    /**
     * Keep database credentials out of the process list. MySQL requires the
     * defaults-file option to be the first mysqldump option. Using
     * --defaults-file also prevents ~/.my.cnf from overriding the app user.
     */
    private function createCredentialsFile(array $config): string
    {
        $path = tempnam(sys_get_temp_dir(), 'venecia-mysql-');
        if ($path === false) {
            throw new \RuntimeException('Unable to create a temporary MySQL credentials file.');
        }

        $quote = static fn (mixed $value): string => '"'.str_replace(
            ["\\", '"', "\r", "\n"],
            ["\\\\", '\\"', '', ''],
            (string) $value,
        ).'"';

        $contents = "[client]\n"
            .'host='.$quote($config['host'] ?? '127.0.0.1')."\n"
            .'port='.(int) ($config['port'] ?? 3306)."\n"
            .'user='.$quote($config['username'] ?? '')."\n"
            .'password='.$quote($config['password'] ?? '')."\n";

        file_put_contents($path, $contents, LOCK_EX);
        chmod($path, 0600);

        return $path;
    }

    private function secureBackupFile(string $path, string $directory): void
    {
        // Cron commonly runs as root while the maintenance dashboard runs as
        // www-data. Match the protected directory ownership so both can manage
        // backups without making database dumps world-readable.
        $owner = fileowner($directory);
        $group = filegroup($directory);

        if ($owner !== false) {
            @chown($path, $owner);
        }
        if ($group !== false) {
            @chgrp($path, $group);
        }

        chmod($path, 0640);
    }

    private function prune(string $dir, int $keep): int
    {
        $files = array_merge(
            glob(rtrim($dir, '/').'/*.sql.gz') ?: [],
            glob(rtrim($dir, '/').'/*.full.tar.gz') ?: [],
        );
        usort($files, fn (string $a, string $b) => strcmp($b, $a));

        $removed = 0;
        foreach (array_slice($files, max(0, $keep)) as $file) {
            if (unlink($file)) {
                $removed++;
            }
        }

        return $removed;
    }

    private function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
