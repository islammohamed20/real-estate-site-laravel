# Enterprise Real Estate Platform

A modular, production-oriented real estate management platform built with Laravel 12, Livewire 3, Volt, TailwindCSS, AlpineJS, Sanctum, Spatie Permission, DomPDF, Chart.js, and MySQL.

## Architecture

- Public Website
- Internal Dashboard
- CRM
- Installment Calculator
- Projects Management
- Units Management
- Reports
- Users & Permissions
- Settings

## Core Principles

- SOLID design
- Repository pattern
- Service layer
- Clean architecture boundaries
- Form requests and policies
- Events, listeners, queues, notifications
- Shared database across modules
- Module independence for future SaaS conversion

## Next Steps

1. Install dependencies with Composer.
2. Configure `.env`.
3. Run migrations and seed roles/permissions.
4. Compile frontend assets.
5. Start the application.

## Notes

This repository is being built as a greenfield enterprise foundation. The implementation emphasizes normalized schemas, reusable domain services, and module isolation rather than demo data.

## آخر تحديثات مشروع فينسيا

تم تحديث إدارة المشروع والمخزون العقاري لمشروع فينسيا كما يلي:

- إضافة قائمتين مترابطتين لاختيار العمارة ثم الدور في نموذج إضافة وتعديل الوحدة، بحيث تظهر أدوار العمارة المحددة فقط.
- إضافة تحقق من جهة الخادم للتأكد من أن العمارة والدور تابعان للمشروع الحالي، وأن الدور تابع للعمارة المختارة.
- إصلاح التهيئة المتأخرة لقوائم Alpine.js حتى يتم استرجاع العمارة والدور المحددين بشكل صحيح عند تعديل الوحدة.
- ضبط القيم الافتراضية للوحدة الجديدة إلى 3 غرف، وحمامين، و3 تراسات.
- تعديل حاسبة الأقساط لعرض الأدوار التي تحتوي على وحدات فقط، وعدم عرض الأدوار الفارغة.
- توحيد بيانات وحدات فينسيا بحيث تكون كل وحدة مرتبطة بالمشروع والعمارة والدور الصحيحين، مع توحيد مواصفات الوحدات.
- إضافة اختبارات وظيفية لنماذج الوحدات وفِلترة أدوار الحاسبة.
- إضافة قواعد لتنظيف المستودع واستبعاد ملفات البيئة والاعتماديات والأصول المولدة وملفات التشغيل والسجلات والكاش من التحديثات المستقبلية.

تتم إدارة مخزون فينسيا من خلال شاشات إدارة المشاريع والوحدات. يجب تنفيذ تغييرات قاعدة البيانات من خلال migrations أو سكربتات استيراد بيانات مضبوطة، وعدم رفع محتوى قاعدة بيانات الإنتاج أو أي مفاتيح وأسرار إلى المستودع.
