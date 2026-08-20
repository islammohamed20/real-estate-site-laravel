<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'name', 'subject', 'body', 'subject_ar', 'body_ar', 'subject_en', 'body_en', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function render(array $variables = [], ?string $locale = null): array
    {
        $replace = [];
        foreach ($variables as $key => $value) {
            $replace['{{'.$key.'}}'] = (string) ($value ?? '');
        }

        $locale = $locale ?: app()->getLocale();
        $locale = in_array($locale, ['ar', 'en'], true) ? $locale : 'en';
        $subject = $this->{'subject_'.$locale} ?: $this->subject;
        $body = $this->{'body_'.$locale} ?: $this->body;

        return ['subject' => strtr($subject, $replace), 'body' => strtr($body, $replace)];
    }

    public static function defaultDefinitions(): array
    {
        return [
            'welcome_customer' => [
                'name' => 'Welcome customer',
                'subject_en' => 'Welcome to Venecia Developments, {{customer_name}}',
                'body_en' => "Hello {{customer_name}},\n\nWelcome to Venecia Developments. Our sales team will contact you shortly.\n\nRegards,\n{{company_name}}",
                'subject_ar' => 'مرحبًا بك في فينسيا للتطوير العقاري، {{customer_name}}',
                'body_ar' => "مرحبًا {{customer_name}}،\n\nأهلًا بك في شركة فينسيا للتطوير العقاري. سيتواصل معك فريق المبيعات قريبًا.\n\nمع تحيات،\n{{company_name}}",
            ],
            'lead_followup' => [
                'name' => 'Lead follow-up reminder',
                'subject_en' => 'Follow-up reminder: {{lead_name}}',
                'body_en' => "Hello {{lead_name}},\n\nThis is a reminder about your real-estate inquiry. We are ready to help you choose the right unit.\n\nRegards,\n{{company_name}}",
                'subject_ar' => 'تذكير بمتابعة العميل: {{lead_name}}',
                'body_ar' => "مرحبًا {{lead_name}}،\n\nنذكرك بخصوص استفسارك العقاري. فريقنا جاهز لمساعدتك في اختيار الوحدة المناسبة.\n\nمع تحيات،\n{{company_name}}",
            ],
            'offer_created' => [
                'name' => 'Offer created',
                'subject_en' => 'Your offer {{offer_number}} — {{company_name}}',
                'body_en' => "Hello {{customer_name}},\n\nYour offer {{offer_number}} has been prepared.\nTotal amount: {{amount}}\n\nYou can contact us for any questions.\n{{company_name}}",
                'subject_ar' => 'عرض السعر الخاص بك {{offer_number}} — {{company_name}}',
                'body_ar' => "مرحبًا {{customer_name}}،\n\nتم إعداد عرض السعر رقم {{offer_number}}.\nالقيمة الإجمالية: {{amount}}\n\nيسعدنا الرد على أي استفسار.\n{{company_name}}",
            ],
            'installment_plan' => [
                'name' => 'Installment plan',
                'subject_en' => 'Your installment plan — {{company_name}}',
                'body_en' => "Hello {{customer_name}},\n\nYour installment plan is ready.\nFinal price: {{amount}}\n\nRegards,\n{{company_name}}",
                'subject_ar' => 'خطة التقسيط الخاصة بك — {{company_name}}',
                'body_ar' => "مرحبًا {{customer_name}}،\n\nخطة التقسيط الخاصة بك أصبحت جاهزة.\nالسعر النهائي: {{amount}}\n\nمع تحيات،\n{{company_name}}",
            ],
            'overdue_followup' => [
                'name' => 'Overdue follow-up alert',
                'subject_en' => 'Overdue follow-up: {{lead_name}}',
                'body_en' => "The follow-up for {{lead_name}} is overdue by {{hours}} hour(s).\nAssigned salesperson: {{agent_name}}",
                'subject_ar' => 'تنبيه متابعة متأخرة: {{lead_name}}',
                'body_ar' => "متابعة العميل {{lead_name}} متأخرة منذ {{hours}} ساعة.\nالمندوب المسؤول: {{agent_name}}",
            ],
        ];
    }
}
