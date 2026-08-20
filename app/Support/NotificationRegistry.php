<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

/**
 * Single source of truth for every notification type the platform sends.
 *
 * Each type maps to a permission that gates delivery: a user only receives a
 * type when their roles/permissions include the mapped permission. Per-user
 * opt-outs (users.notification_preferences) are applied on top of that.
 */
class NotificationRegistry
{
    public const TYPES = [
        'customer' => [
            'permission' => 'receive notification.customer',
            'title_en' => 'New customer',
            'title_ar' => 'عميل جديد',
        ],
        'offer' => [
            'permission' => 'receive notification.offer',
            'title_en' => 'New offer',
            'title_ar' => 'عرض جديد',
        ],
        'plan' => [
            'permission' => 'receive notification.plan',
            'title_en' => 'New installment plan',
            'title_ar' => 'خطة أقساط جديدة',
        ],
        'whatsapp_unassigned' => [
            'permission' => 'receive notification.whatsapp',
            'title_en' => 'Unassigned WhatsApp conversation',
            'title_ar' => 'محادثة واتساب غير مُسنَدة',
        ],
        'whatsapp_reassigned' => [
            'permission' => 'receive notification.whatsapp',
            'title_en' => 'WhatsApp conversation reassigned',
            'title_ar' => 'إعادة توزيع محادثة واتساب',
        ],
        'login_failed' => [
            'permission' => 'receive notification.security',
            'title_en' => 'Failed login attempt',
            'title_ar' => 'محاولة دخول فاشلة',
        ],
        'followup_overdue' => [
            'permission' => 'receive notification.followups',
            'title_en' => 'Overdue follow-up',
            'title_ar' => 'متابعة متأخرة',
        ],
        'trash_warning' => [
            'permission' => 'receive notification.trash',
            'title_en' => 'Trash purge warning',
            'title_ar' => 'تحذير حذف من المهملات',
        ],
    ];

    public static function permissionFor(string $type): ?string
    {
        return self::TYPES[$type]['permission'] ?? null;
    }

    public static function label(string $type, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return self::TYPES[$type]['title_'.$locale] ?? self::TYPES[$type]['title_en'] ?? $type;
    }

    /**
     * All registered types as [key => [permission, title_en, title_ar]].
     *
     * @return array<string, array{permission: string, title_en: string, title_ar: string}>
     */
    public static function types(): array
    {
        return self::TYPES;
    }

    /**
     * Active users who are allowed to receive the given notification type and
     * have not opted out of it in their personal preferences.
     *
     * A user holding the wildcard "receive notification.all" permission
     * receives every type.
     */
    public static function recipients(string $type): Collection
    {
        $permission = self::permissionFor($type);

        $query = User::query()->where('is_active', true);

        $grantingPermissions = array_values(array_filter(
            ['receive notification.all', $permission],
            fn (?string $name) => $name !== null && Permission::query()->where('name', $name)->exists(),
        ));

        if ($grantingPermissions !== []) {
            $query->permission($grantingPermissions);
        } else {
            // Fallback to the legacy pool when no permission has been seeded yet.
            $query->where(fn (Builder $roleQuery) => $roleQuery->role(['Administrator', 'Sales Manager']));
        }

        return $query->get()
            ->filter(fn (User $user) => $user->acceptsNotification($type))
            ->values();
    }
}
