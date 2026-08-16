<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class CrmActivityNotification extends Notification
{
    /**
     * @param  'customer'|'offer'|'plan'|'whatsapp_unassigned'|'login_failed'  $type
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $type,
        public array $payload,
        public ?string $actorName = null,
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
        $actor = $this->actorName ?? __('System');
        $amount = isset($this->payload['amount']) ? number_format((float) $this->payload['amount'], 0) : null;

        return match ($this->type) {
            'customer' => [
                'title_ar' => 'عميل جديد',
                'title_en' => 'New customer',
                'message_ar' => "{$actor} أضاف عميلاً جديداً: {$this->payload['name']}",
                'message_en' => "{$actor} added a new customer: {$this->payload['name']}",
                'action_url' => $this->payload['action_url'] ?? '#',
                'type' => 'customer',
                'actor_name' => $this->actorName,
            ],
            'offer' => [
                'title_ar' => 'عرض جديد',
                'title_en' => 'New offer',
                'message_ar' => "{$actor} أضاف عرضاً {$this->payload['offer_number']} للعميل {$this->payload['customer_name']}".($amount !== null ? " بقيمة {$amount}" : ''),
                'message_en' => "{$actor} added offer {$this->payload['offer_number']} for customer {$this->payload['customer_name']}".($amount !== null ? " worth {$amount}" : ''),
                'action_url' => $this->payload['action_url'] ?? '#',
                'type' => 'offer',
                'actor_name' => $this->actorName,
            ],
            'plan' => [
                'title_ar' => 'خطة أقساط جديدة',
                'title_en' => 'New installment plan',
                'message_ar' => "{$actor} حفظ خطة أقساط للعميل {$this->payload['customer_name']}".($amount !== null ? " بقيمة {$amount}" : '').(isset($this->payload['unit_number']) ? " — الوحدة {$this->payload['unit_number']}" : ''),
                'message_en' => "{$actor} saved an installment plan for customer {$this->payload['customer_name']}".($amount !== null ? " worth {$amount}" : '').(isset($this->payload['unit_number']) ? " — unit {$this->payload['unit_number']}" : ''),
                'action_url' => $this->payload['action_url'] ?? '#',
                'type' => 'plan',
                'actor_name' => $this->actorName,
            ],
            'whatsapp_unassigned' => [
                'title_ar' => 'محادثة واتساب بانتظار الرد',
                'title_en' => 'WhatsApp conversation awaiting reply',
                'message_ar' => 'محادثة جديدة غير مُسنَدة بانتظار الرد منذ أكثر من ساعة: '.($this->payload['customer_name'] ?? $this->payload['customer_phone'] ?? '').' — '.($this->payload['hours'] ?? 1).' ساعة',
                'message_en' => 'New unassigned conversation awaiting a reply for over an hour: '.($this->payload['customer_name'] ?? $this->payload['customer_phone'] ?? '').' — '.($this->payload['hours'] ?? 1).'h',
                'action_url' => $this->payload['action_url'] ?? '#',
                'type' => 'whatsapp_unassigned',
                'conversation_id' => $this->payload['conversation_id'] ?? null,
                'actor_name' => $this->actorName,
            ],
            'login_failed' => [
                'title_ar' => 'محاولة دخول فاشلة',
                'title_en' => 'Failed login attempt',
                'message_ar' => 'محاولة دخول فاشلة لحساب '.
                    ($this->payload['email'] ?? __('Unknown')).
                    ' من IP '.
                    ($this->payload['ip'] ?? '-').
                    ($this->payload['device'] ? ' على جهاز '.$this->payload['device'] : '').
                    ($this->payload['known_user'] === false ? ' (حساب غير مسجل)' : ''),
                'message_en' => 'Failed login attempt for '.
                    ($this->payload['email'] ?? __('Unknown')).
                    ' from IP '.
                    ($this->payload['ip'] ?? '-').
                    ($this->payload['device'] ? ' on '.$this->payload['device'] : '').
                    ($this->payload['known_user'] === false ? ' (unknown account)' : ''),
                'action_url' => $this->payload['action_url'] ?? route('dashboard.settings.index'),
                'type' => 'login_failed',
                'email' => $this->payload['email'] ?? null,
                'ip' => $this->payload['ip'] ?? null,
                'device' => $this->payload['device'] ?? null,
                'known_user' => $this->payload['known_user'] ?? null,
                'actor_name' => $this->actorName,
            ],
        };
    }

    /**
     * Notify all Administrator and Sales Manager users.
     */
    public static function notifyManagers(self $notification): void
    {
        $recipients = User::query()
            ->where(fn (Builder $query) => $query->role(['Administrator', 'Sales Manager']))
            ->get();

        if ($recipients->isNotEmpty()) {
            NotificationFacade::send($recipients, $notification);
        }
    }
}
