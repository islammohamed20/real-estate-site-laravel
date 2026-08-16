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
