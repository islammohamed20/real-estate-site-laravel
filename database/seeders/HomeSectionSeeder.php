<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'key' => 'hero',
                'title' => __('ابتكار العمران.. وصناعة مجتمعات'),
                'subtitle' => __('سكنية فاخرة'),
                'content' => __('نبتكر حلولاً معمارية متكاملة تدمج بين الرفاهية والسكن الراقي في أرقى المواقع الحيوية، مع أنظمة سداد مرنة وتقسيط مباشر يصل إلى 5 سنوات بدون فوائد.'),
                'sort_order' => 1,
            ],
            [
                'key' => 'pillars',
                'title' => __('ركائزنا الاستراتيجية'),
                'subtitle' => __('قيمنا التي نؤمن بها'),
                'content' => __('أربع ركائز نبني عليها كل مشاريعنا لضمان أعلى مستويات الجودة والثقة.'),
                'sort_order' => 2,
            ],
            [
                'key' => 'projects',
                'title' => __('Integrated Communities for Modern Living'),
                'subtitle' => __('Our Developments'),
                'content' => null,
                'sort_order' => 3,
            ],
            [
                'key' => 'units',
                'title' => __('Selected Units for Your Next Move'),
                'subtitle' => __('فرص استثمارية مميزة'),
                'content' => null,
                'sort_order' => 4,
            ],
            [
                'key' => 'calculator',
                'title' => __('احسب خطة التقسيط المناسبة لك في ثوانٍ'),
                'subtitle' => __('حاسبة الأقساط التفاعلية المباشرة'),
                'content' => __('اختر مقدم التعاقد وسنوات السداد المناسبة لظروفك الاستثمارية مع معاينة حية للأقساط وإمكانية طباعة ملف PDF معتمد.'),
                'sort_order' => 5,
            ],
        ];

        foreach ($sections as $section) {
            HomeSection::query()->updateOrCreate(
                ['key' => $section['key']],
                [
                    'title' => $section['title'],
                    'subtitle' => $section['subtitle'],
                    'content' => $section['content'],
                    'sort_order' => $section['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
