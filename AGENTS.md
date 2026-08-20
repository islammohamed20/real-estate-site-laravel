# Project Notes — Venecia Real-Estate Site

## Verification commands

- Run the full test suite:
  ```bash
  php artisan config:clear   # REQUIRED before tests — a cached config (bootstrap/cache/config.php) breaks them (419 CSRF + wrong DB)
  php artisan test tests/Feature
  ```
- Rebuild Blade views: `php artisan view:cache`
- Build assets: `npm run build`
- All tests passing: `102 passed (294 assertions)`

## Gotchas

- **Config cache vs tests**: if `bootstrap/cache/config.php` exists (created by `php artisan config:cache`, e.g. during a deploy), `APP_ENV` is pinned to `local` and the DB is the production one — PHPUnit's `phpunit.xml` env (`testing` + sqlite `:memory:`) is ignored. Symptom: mass `419 CSRF` failures and `500`s. Fix: `php artisan config:clear` before running tests, then re-cache for production if needed.
- **Blade compiler + multiline `@php` with `?->`**: the Blade compiler (via Livewire's extension) leaves multiline `@php` blocks containing nullsafe operators (`?->`) unprocessed — the raw `@php`/`?>` text lands in the compiled view and variables stay undefined. Fix: compute the values in the controller and pass them to the view (see `SettingsController::index` — `userPrefs`/`allowedTypes`).
- **Sales calculator discount (Excel model)**: `InstallmentCalculatorService` uses the spreadsheet formula — discount% = max(0, down-payment% − 10) × 0.30, applied to the unit price (with excellence). Cash = 100% down → 27%. The same math must stay in sync with the Alpine live preview in `resources/views/installments/index.blade.php` (`discountPercent` getter) and with `tests/Feature/InstallmentCalculatorTest.php`.
