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

## Recent Venecia Updates

The project-management and inventory workflows were updated for the Venecia development:

- Added dependent building and floor selectors to the unit create/edit form. Staff select a building first, then only floors belonging to that building are available.
- Added server-side validation to ensure that the selected building and floor belong to the current project and that the floor belongs to the selected building.
- Added delayed Alpine.js initialization so existing building and floor selections are restored correctly when editing a unit.
- Updated new-unit defaults to three bedrooms, two bathrooms, and three terraces.
- Restricted the installment calculator floor list to floors that contain at least one unit; empty floors are no longer shown.
- Normalized Venecia inventory records so every unit has a valid project, building, and floor relationship, with consistent unit specifications.
- Added and maintained feature tests covering project unit forms and calculator floor filtering.
- Added repository hygiene rules to exclude environment files, dependencies, generated assets, runtime storage, logs, and cache files from future commits.

The Venecia inventory data is maintained through the project and unit management screens. Database changes should be delivered through migrations or controlled data-import scripts rather than committing production database contents or secrets.
