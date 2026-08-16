# Tech Stack — Real Estate Enterprise Platform

## Frontend

| التقنية | الإصدار | الاستخدام |
|---------|---------|-----------|
| **Blade** | مدمج مع Laravel | محرك القوالب الرئيسي لكل الصفحات |
| **Tailwind CSS** | `^3.4.17` | إطار التصميم بالكامل — ألوان مخصصة عبر CSS variables (`brand`, `slate`, `violet`, `emerald`, `rose`, `sky`) |
| **Alpine.js** | `^3.14.9` | تفاعلية الواجهة — تبويبات، طي/فتح حقول، فلترة متسلسلة، نوافذ منبثقة |
| **Chart.js** | `^4.4.8` | رسوم بيانية في لوحة التحكم |
| **Vite** | `^6.0.11` | bundler و dev server عبر `laravel-vite-plugin` |
| **PostCSS** + **Autoprefixer** | `^8.5` / `^10.4` | معالجة CSS |
| **@tailwindcss/forms** | `^0.5.10` | تنسيق حقول النماذج |
| **PWA** (Service Worker) | مخصص | `resources/js/pwa.js` — دعم التثبيت كتطبيق وعمل offline |
| **Dark Mode** | `class` strategy | تبديل ثيم فاتح/داكن عبر `localStorage` |
| **Toast Notifications** | مخصص | `resources/js/app.js` — إشعارات منبثقة |
| **Confirm Modal** | مخصص | `window.confirmAction()` — نافذة تأكيد عامة |
| **الخطوط** | Plus Jakarta Sans + Cairo | دعم عربي/إنجليزي |

## Backend

| التقنية | الإصدار | الاستخدام |
|---------|---------|-----------|
| **PHP** | `^8.4` | لغة البرمجة |
| **Laravel** | `^12.0` | الإطار الرئيسي |
| **Livewire** | `^3.6` | تفاعل الواجهة بدون كتابة JS |
| **Livewire Volt** | `^1.8` | API وظيفي لكتابة مكونات Livewire |
| **Laravel Sanctum** | `^4.0` | مصادقة API / SPA tokens |
| **Spatie Permission** | `^6.17` | نظام الأدوار والصلاحيات (RBAC) |
| **Spatie Activity Log** | `^4.10` | سجل نشاط المستخدمين والتغييرات |
| **Maatwebsite Excel** | `^3.1` | استيراد/تصدير Excel |
| **Barryvdh DomPDF** | `^3.1` | توليد PDF |
| **mPDF** | `^8.3` | توليد PDF بديل (دعم عربي أفضل) |
| **Guzzle HTTP** | `^7.9` | طلبات HTTP خارجية |
| **PHPUnit** | `^11.5` | اختبارات |
| **Faker** | `^1.24` | بيانات وهمية للاختبارات |
| **Laravel Pint** | `^1.18` | تنسيق الكود (CS fixer) |
| **Mockery** | `^1.6` | Mocking في الاختبارات |

## Database & Infrastructure

| التقنية | التفاصيل |
|---------|----------|
| **MySQL** | قاعدة البيانات الافتراضية (`utf8mb4_unicode_ci`, strict mode) |
| **SQLite** | مدعومة كـ fallback (للاختبارات) |
| **Redis** | متاح للـ Cache و Queue و Session (عبر `phpredis`) |
| **Cache** | افتراضي: `database` (جدول `cache` + `cache_locks`) |
| **Queue** | افتراضي: `database` (جدول `jobs`) — مع دعم Redis |
| **Session** | افتراضي: `database` (جدول `sessions`) |
| **Migrations** | 50 ملف migration — تغطي users, projects, units, leads, CRM, tasks, notes, offers, reservations, documents, installment plans, permissions |
| **Polymorphic Relations** | Notes و Tasks مرتبطة polymorphic بـ leads, customers, deals, organizations, contacts |

## بنية المشروع

- **50 جدول** في قاعدة البيانات
- **CRM كامل**: leads, customers, deals, pipelines, stages, organizations, contacts, activities, offers, reservations, tasks, follow-ups, notes
- **بحث شامل**: global search عبر leads, customers, offers, reservations, projects, units
- **مكتبة المستندات**: رفع وتحميل وإدارة الملفات مرتبطة بأي سجل CRM (polymorphic)
- **تقارير وتحليلات**: CRM dashboard reports مع مؤشرات الأداء ورسوم بيانية
- **إدارة عقارات**: projects, phases, buildings, floors, units
- **مالية**: installment templates, installment plans, offers, reservations
- **أمان**: roles, permissions, login histories, audit logs, personal access tokens
- **تعدد اللغات**: `lang/en.json` + `lang/ar.json` (929+ مفتاح ترجمة)
- **PWA**: Service Worker + manifest للتثبيت كتطبيق موبايل
