<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TrashedPlansPurgeWarning extends Notification
{
    /**
     * @param  list<array{id: int, customer_name?: string, unit_number?: string, deleted_at: string}>  $plans
     */
    public function __construct(
        public array $plans,
        public int $days,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $count = count($this->plans);
        $days = $this->days;

        return [
            'title_ar' => 'تنبيه: حذف نهائي قادم لخطط الأقساط',
            'title_en' => 'Warning: installment plans will be permanently deleted',
            'message_ar' => "{$count} ".($count === 1 ? 'خطة أقساط في سلة المحذوفات ستُحذف نهائياً' : 'خطط أقساط في سلة المحذوفات ستُحذف نهائياً')." خلال {$days} ".($days === 1 ? 'يوم واحد' : 'أيام').'. استعدها الآن من سلة المحذوفات قبل فوات الأوان.',
            'message_en' => "{$count} installment plan".($count === 1 ? '' : 's').' in the trash will be permanently deleted in '.$days.' day'.($days === 1 ? '' : 's').'. Restore them now from the trash before it is too late.',
            'plan_ids' => array_column($this->plans, 'id'),
            'action_url' => route('dashboard.crm.plans.trash'),
        ];
    }
}
