<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Key-value store for the sales automation toggles and thresholds,
 * editable from the dashboard (Settings → Automation).
 */
class AutomationSetting extends Model
{
    use HasFactory;

    public const DEFAULTS = [
        // Lead distribution
        'lead_auto_assign' => '1',
        // Response SLA
        'sla_enabled' => '1',
        'sla_alert_minutes' => '30',
        'sla_escalate_minutes' => '120',
        // Follow-up alerts
        'followup_alerts_enabled' => '1',
        // Weekly WhatsApp report
        'weekly_report_enabled' => '1',
        'weekly_report_day' => '1', // Monday
        'weekly_report_time' => '10:00',
        // Monthly scorecard
        'scorecard_enabled' => '1',
        'scorecard_day' => '1',
        'scorecard_time' => '02:00',
        // Scheduled jobs / maintenance
        'database_backup_enabled' => '1',
        'database_backup_time' => '02:30',
        'database_backup_keep' => '30',
        'whatsapp_sync_enabled' => '1',
        'whatsapp_unassigned_enabled' => '1',
        'queue_worker_enabled' => '1',
    ];

    protected $fillable = ['key', 'value'];

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Read a setting with a default (falling back to the shipped defaults).
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            $value = static::query()->where('key', $key)->value('value');
        } catch (\Throwable) {
            // The scheduler is bootstrapped before migrations in fresh test
            // databases; fall back safely to the shipped default.
            $value = null;
        }

        if ($value === null) {
            return $default ?? self::DEFAULTS[$key] ?? null;
        }

        return $value;
    }

    /**
     * Boolean convenience accessor.
     */
    public static function enabled(string $key, bool $default = true): bool
    {
        return filter_var(static::get($key, $default ? '1' : '0'), FILTER_VALIDATE_BOOL);
    }

    /**
     * Integer convenience accessor.
     */
    public static function integer(string $key, int $default = 0): int
    {
        return max(0, (int) static::get($key, (string) $default));
    }

    /**
     * Persist one (or many) key/value pairs.
     *
     * @param  array<string, mixed>  $values
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value],
            );
        }
    }

    /**
     * Ensure every shipped default exists (used by a seeder / first boot).
     */
    public static function ensureDefaults(): void
    {
        try {
            foreach (self::DEFAULTS as $key => $value) {
                static::query()->firstOrCreate(['key' => $key], ['value' => $value]);
            }
        } catch (\Throwable) {
            // Ignore before the automation_settings migration exists.
        }
    }
}
