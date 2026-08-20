<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Console\Commands\BackupDatabase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MaintenanceController extends Controller
{
    public function index(): View
    {
        \App\Models\AutomationSetting::ensureDefaults();
        $backupDir = app(BackupDatabase::class)->backupDirectory();

        $backups = collect(glob(rtrim($backupDir, '/').'/*.sql.gz') ?: [])
            ->map(fn (string $file) => [
                'name' => basename($file),
                'size' => (int) filesize($file),
                'human_size' => $this->humanSize((int) filesize($file)),
                'modified_at' => date('Y-m-d H:i:s', filemtime($file)),
                'path' => $file,
            ])
            ->sortByDesc('modified_at')
            ->values();

        return view('dashboard.maintenance.index', [
            'backupDir' => $backupDir,
            'backups' => $backups,
            'lastBackupAt' => $backups->first()['modified_at'] ?? null,
            'dbSize' => $this->dbSize(),
            'diskFree' => disk_free_space('/') !== false ? $this->humanSize((int) disk_free_space('/')) : '—',
            'uploadsSize' => $this->uploadsSize(),
            'configCacheExists' => is_file(base_path('bootstrap/cache/config.php')),
            'viewCacheExists' => is_file(base_path('bootstrap/cache/views.php')) || count(glob(storage_path('framework/views/*.php')) ?: []) > 2,
            'maintenanceMode' => app()->isDownForMaintenance(),
            'scheduleOutput' => $this->scheduleList(),
            'jobSettings' => \App\Models\AutomationSetting::query()->pluck('value', 'key'),
        ]);
    }

    public function updateScheduledJobs(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'database_backup_enabled' => ['boolean'],
            'database_backup_time' => ['required', 'date_format:H:i'],
            'database_backup_keep' => ['required', 'integer', 'min:1', 'max:365'],
            'whatsapp_sync_enabled' => ['boolean'],
            'whatsapp_unassigned_enabled' => ['boolean'],
            'queue_worker_enabled' => ['boolean'],
        ]);

        \App\Models\AutomationSetting::setMany($validated);

        return back()->with('status', __('Scheduled jobs settings saved.'));
    }

    public function createBackup(): RedirectResponse
    {
        $exitCode = Artisan::call('backup:database');
        $output = trim(Artisan::output());

        if ($exitCode !== 0) {
            return back()->withErrors(['backup' => $output ?: __('Backup failed.')]);
        }

        return back()->with('status', $output ?: __('Database backup created.'));
    }

    public function download(Request $request): BinaryFileResponse
    {
        $file = $this->resolveBackupFile((string) $request->route('file'));

        abort_if($file === null, 404);

        return response()->download($file);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $file = $this->resolveBackupFile((string) $request->route('file'));

        abort_if($file === null, 404);

        unlink($file);

        return back()->with('status', __('Backup deleted.'));
    }

    /**
     * Restore the database from a backup file. Destructive — the current
     * database is replaced entirely, so a fresh backup is taken first.
     */
    public function restore(Request $request): RedirectResponse
    {
        $file = $this->resolveBackupFile((string) $request->route('file'));

        abort_if($file === null, 404);

        // Safety net: snapshot the current state before overwriting it.
        Artisan::call('backup:database');
        $safety = trim(Artisan::output());

        $connection = config('database.default') ?: 'mysql';
        $config = config("database.connections.{$connection}");

        $command = sprintf(
            'gunzip -c %s | mysql --host=%s --port=%s --user=%s --password=%s %s',
            escapeshellarg($file),
            escapeshellarg((string) $config['host']),
            escapeshellarg((string) ($config['port'] ?? 3306)),
            escapeshellarg((string) $config['username']),
            escapeshellarg((string) $config['password']),
            escapeshellarg((string) $config['database']),
        );

        exec($command.' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            return back()->withErrors([
                'restore' => __('Restore failed: :error', ['error' => implode("\n", $output)]),
            ]);
        }

        return back()->with('status', __('Database restored from :file. A safety backup was taken first: :safety', [
            'file' => basename($file),
            'safety' => $safety,
        ]));
    }

    public function clearCache(Request $request): RedirectResponse
    {
        $type = (string) $request->input('type', 'all');

        $commands = match ($type) {
            'config' => ['config:clear'],
            'view' => ['view:clear'],
            'route' => ['route:clear'],
            'app' => ['cache:clear'],
            default => ['config:clear', 'view:clear', 'route:clear', 'cache:clear'],
        };

        $output = [];
        foreach ($commands as $command) {
            Artisan::call($command);
            $output[] = trim(Artisan::output());
        }

        return back()->with('status', __('Cache cleared: :commands', ['commands' => implode(' + ', $commands)]));
    }

    /**
     * Safely resolve a backup file inside the backup directory (no traversal).
     */
    private function resolveBackupFile(string $name): ?string
    {
        $dir = app(BackupDatabase::class)->backupDirectory();
        $file = realpath(rtrim($dir, '/').'/'.basename($name));

        if ($file === false || ! str_starts_with($file, realpath($dir) ?: $dir)) {
            return null;
        }

        return is_file($file) ? $file : null;
    }

    private function dbSize(): string
    {
        try {
            $database = config('database.connections.'.(config('database.default') ?: 'mysql').'.database');
            $size = (float) DB::table('information_schema.tables')
                ->where('table_schema', $database)
                ->sum(DB::raw('data_length + index_length'));

            return $this->humanSize((int) $size);
        } catch (\Throwable) {
            return '—';
        }
    }

    private function uploadsSize(): string
    {
        $path = storage_path('app/public');
        if (! is_dir($path)) {
            return '0 B';
        }

        exec('du -sb '.escapeshellarg($path).' 2>/dev/null', $output, $exitCode);

        if ($exitCode !== 0 || ! isset($output[0])) {
            return '—';
        }

        return $this->humanSize((int) (explode("\t", $output[0])[0] ?? 0));
    }

    private function scheduleList(): array
    {
        Artisan::call('schedule:list');
        $lines = array_filter(explode("\n", Artisan::output()), fn (string $line) => trim($line) !== '');

        return array_slice(array_values($lines), 0, 20);
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
