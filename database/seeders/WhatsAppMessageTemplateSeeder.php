<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class WhatsAppMessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'رسالة ترحيب',
                'body' => "أهلاً بك في فينيسيا للتطوير العقاري.\nشكراً لتواصلك معنا، فريقنا جاهز لمساعدتك في اختيار وحدتك المناسبة.",
            ],
            [
                'name' => 'متابعة استفسار',
                'body' => "مرحباً، لاحظنا استفسارك عن مشاريعنا.\nهل تريد معرفة المزيد عن الوحدات المتاحة والأسعار؟",
            ],
            [
                'name' => 'عرض السعر',
                'body' => "مرحباً، يسعدنا إرسال عرض السعر التفصيلي لوحدتك.\nهل تريد أن نرسله لك الآن؟",
            ],
            [
                'name' => 'خطة الأقساط',
                'body' => "مرحباً، نقوم بتجهيز خطة الأقساط المناسبة لك حالياً.\nستصلك التفاصيل قريباً.",
            ],
            [
                'name' => 'حجز معاينة',
                'body' => "مرحباً، يسعدنا ترتيب موعد لمعاينة الوحدة.\nما هو الموعد الأنسب لك؟",
            ],
            [
                'name' => 'متابعة نهائية',
                'body' => "شكراً لتواصلك مع فينيسيا للتطوير العقاري.\nنسعد دائماً بخدمتك، لا تتردد في التواصل معنا في أي وقت.",
            ],
        ];

        foreach ($templates as $template) {
            MessageTemplate::query()->updateOrCreate(
                ['name' => $template['name']],
                [
                    'body' => $template['body'],
                    'lang' => 'ar',
                    'is_active' => true,
                ]
            );
        }
    }
}
