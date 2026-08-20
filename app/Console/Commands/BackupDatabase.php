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
    protected $signature = 'backup:database {--keep=30 : Number of backups to keep}';

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

        $filename = now()->format('Y-m-d_H-i-s').'.sql.gz';
        $path = rtrim($dir, '/').'/'.$filename;

        // Note: this MariaDB client build ignores MYSQL_PWD, so the password
        // is passed explicitly (escaped) to mysqldump.
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --events %s | gzip > %s',
            escapeshellarg((string) $config['host']),
            escapeshellarg((string) ($config['port'] ?? 3306)),
            escapeshellarg((string) $config['username']),
            escapeshellarg((string) $config['password']),
            escapeshellarg((string) $config['database']),
            escapeshellarg($path),
        );

        exec($command.' 2>&1', $output, $exitCode);

        if ($exitCode !== 0 || ! is_file($path) || filesize($path) === 0) {
            $this->error('Backup failed: '.implode("\n", $output));

            return self::FAILURE;
        }

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

    private function ensureDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (! is_writable($dir)) {
            chmod($dir, 0775);
        }
    }

    private function prune(string $dir, int $keep): int
    {
        $files = glob(rtrim($dir, '/').'/*.sql.gz') ?: [];
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
