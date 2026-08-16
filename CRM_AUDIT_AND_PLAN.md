# CRM Audit Report & Modernization Plan

> Generated after Phase 1 inspection of the existing Laravel 12 Real Estate CRM.

## 1. Executive Summary

The existing CRM has a **solid foundation** with:
- Laravel 12, PHP 8.4, Tailwind, Alpine.js, Vite
- Lead/customer creation, tasks, notes, organizations, contacts, deals
- A configurable pipeline/stage system for deals
- Polymorphic tasks/notes
- Spatie Permission RBAC
- PWA, dark mode, RTL

**Test status:** `14 passed (30 assertions)` — all existing CRM tests pass.

However, the CRM is still **operating like a dashboard with quick forms**, not a full professional real-estate sales workflow. Major gaps exist in: lead management, follow-ups, calls/meetings/site visits, customer 360, mobile navigation, global search, tags, lead sources, calendar, advanced reporting, and document management.

---

## 2. Existing CRM Architecture Audit

### 2.1 Routes (web.php)

| Area | Routes | Status |
|------|--------|--------|
| CRM index | `GET /dashboard/crm` | ✅ Exists |
| Quick lead creation | `POST /dashboard/crm/leads` | ✅ Exists |
| Tasks | `POST / PUT / DELETE /dashboard/crm/tasks` | ✅ Exists |
| Notes | `POST / DELETE /dashboard/crm/notes` | ✅ Exists |
| Organizations | `POST /dashboard/crm/organizations` | ✅ Exists |
| Contacts | `POST /dashboard/crm/contacts` | ✅ Exists |
| Deals | Full resource + stage + activities | ✅ Exists |
| Follow-ups | ❌ No dedicated routes | Missing |
| Calls | ❌ No dedicated routes | Missing |
| Meetings | ❌ No dedicated routes | Missing |
| Site visits | ❌ No dedicated routes | Missing |
| Calendar | ❌ No dedicated routes | Missing |
| Global search | ❌ No dedicated routes | Missing |
| Lead sources | ❌ No dedicated routes | Missing |
| Documents | ❌ No dedicated routes | Missing |

### 2.2 Controllers

| Controller | Responsibility | Notes |
|------------|----------------|-------|
| `CrmController` | CRM dashboard, quick forms for leads, tasks, notes, orgs, contacts | Uses `LeadCreationService`. No edit/update. No detail view. |
| `Crm\DealController` | Full deal CRUD, stage movement, activities | Strong implementation. |
| `LeadInquiryController` | Public website lead capture | Reuses `LeadCreationService`. |
| `InstallmentCalculatorController` | Standalone calculator (PDF, result) | ✅ Independent from CRM. |
| `DashboardController` | Main dashboard stats | Basic. |
| `ReportsController` | Stub (`index` returns 0 bytes view? needs check) | Likely empty. |

### 2.3 Models

| Model | Fillable / Key Fields | Relationships | Gaps |
|-------|----------------------|---------------|------|
| `Lead` | name, phone, email, national_id, address, occupation, budget, stage, source, notes, last_contacted_at, follow_up_at, converted_at | customer, assignedSales, interestedProjects, stageHistory, offers, reservations, tasks, notes | No `whatsapp`, `priority`, `tags`, `assigned_user` history, no `interestedUnits` (only projects). |
| `Customer` | name, phone, email, national_id, address, occupation, budget/min/max, source, notes | leads, interestedProjects, tasks, notes | No `whatsapp`, `tags`, 360 relationships. |
| `FollowUp` | lead_id, customer_id, assigned_to, follow_up_at, channel, status, notes | lead, customer, assignee | No dedicated controller/routes. Not surfaced in UI. |
| `Task` | title, description, assigned_to, taskable polymorph, priority, status, due_at, completed_at | assignee, creator, taskable | Only supports `lead` and `customer` in `CrmController`, not deal/project/unit. |
| `Note` | noteable polymorph, user_id, body, type, noted_at | noteable, user | ✅ Good. |
| `Offer` | offer_number, customer/lead, unit, installment plan, amounts, status | customer, lead, sales, project, unit, installments | Has deal_id? **No.** No link to deals or CRM pipeline. |
| `Reservation` | reservation_number, customer/lead, unit, reserved_at, expires_at, deposit, status | customer, lead, sales, unit | No deal_id. No conflict validation in controller. |
| `CrmOrganization` | name, industry, website, phone, email, address, city, country, tax_id, notes | contacts, deals | No `assigned_user`, tags. |
| `CrmContact` | first_name, last_name, email, phone, mobile, job_title, source, is_primary, notes | organization, deals, activities | ✅ Good. |
| `CrmDeal` | title, pipeline/stage, org/contact/lead/customer, project/unit, value, currency, expected_close, priority, source, status, closed_at | all related + activities + stageHistory | ✅ Strong. |
| `CrmActivity` | deal_id, contact_id, type, subject, body, due_at, completed_at, outcome, duration, activityable | deal, contact, creator | Hard-linked to `deal_id`; cannot be a top-level activity on a lead/customer. |
| `CrmPipeline` / `CrmStage` | name, color, probability, sort_order, is_active | stages/deals | No description field on stage. |

### 2.4 Migrations — Existing Tables

| Table | Purpose | Can be Extended? |
|-------|---------|------------------|
| `leads` | Core lead | ✅ Add columns: whatsapp, priority, campaign, unit_type, bedrooms, required_area, preferred_payment_plan, tags (pivot), status |
| `customers` | Core customer | ✅ Add columns: whatsapp, tags (pivot) |
| `follow_ups` | Follow-up records | ✅ Reuse; add `type`, `reminder`, `duration`, `result` if needed |
| `tasks` | Polymorphic tasks | ✅ Add `reminder` column; taskable already supports any model |
| `notes` | Polymorphic notes | ✅ Reuse |
| `offers` | Price offers | ✅ Add `deal_id`, `status` expanded, `expiration` exists |
| `reservations` | Unit reservations | ✅ Add conflict unique index; add `deal_id` |
| `crm_organizations` | B2B orgs | ✅ Add `assigned_to` if needed |
| `crm_contacts` | Org contacts | ✅ Reuse |
| `crm_pipelines` / `crm_stages` | Deal pipeline | ✅ Add `description` to stages |
| `crm_deals` | Deal opportunities | ✅ Reuse; add `original_price`, `discount`, `final_price` if not using `value` |
| `crm_activities` | Deal activities | ⚠️ Reuse but needs `lead_id`/`customer_id` to be top-level |
| `lead_stage_histories` | Lead stage changes | ✅ Reuse |
| `crm_deal_stage_histories` | Deal stage changes | ✅ Reuse |

### 2.5 Database Schema — Missing Columns / Tables

#### New columns on existing tables (no new table needed)

- `leads.whatsapp`
- `leads.priority` (low/normal/high/urgent)
- `leads.campaign`
- `leads.unit_type`
- `leads.bedrooms`
- `leads.required_area`
- `leads.preferred_payment_plan`
- `customers.whatsapp`
- `follow_ups.type` (call/whatsapp/email/meeting/site_visit/other)
- `follow_ups.reminder` boolean
- `tasks.reminder` boolean
- `offers.deal_id` (nullable FK)
- `reservations.deal_id` (nullable FK)
- `crm_organizations.assigned_to` (nullable FK)
- `crm_stages.description`

#### New tables required

- `lead_sources` (id, name, color, is_active, is_default, sort_order) — admin-managed
- `tags` (id, name, color, is_active) + `taggables` polymorphic pivot
- `lead_unit_interests` (lead_id, unit_id, priority, notes, status) OR reuse a polymorphic `interests` table
- `documents` / `media` — polymorphic file attachments
- `site_visits` (customer_id, lead_id, project_id, unit_id, sales_id, date, time, status, result, notes)
- `meetings` (customer_id, lead_id, deal_id, assigned_user, date, time, location, status, notes) — or reuse `follow_ups` with type=meeting
- `calls` — can reuse `crm_activities` if expanded

### 2.6 Permissions & Policies

| Role | Permissions |
|------|-------------|
| Administrator | all |
| Sales Manager | manage projects, units, crm, reports, offers, reservations, templates |
| Sales Executive | manage crm, create offers/reservations, manage plans |
| Viewer | view reports |

**Issues:**
- No fine-grained permissions for `view own leads`, `view team leads`, `assign leads`.
- Policies (`BasePolicy`) are generic and do NOT enforce "Sales Executive sees only assigned leads".
- Routes use `role:` middleware, not `can:` policy middleware.
- No `LeadPolicy` ownership check.
- No `CrmDeal` policy.

### 2.7 Activity Logging

- Spatie Activity Log is **installed but not integrated** in any model (grep found no `logsActivity` trait usage).
- `AuditLog` model exists but no explicit audit logging calls in controllers.
- `LeadStageChanged` event exists but no listener was found in the audit.
- `LeadStageHistory` and `CrmDealStageHistory` tables exist and are populated by services.

**Gap:** Need to wire Spatie Activity Log to lead/deal/customer/offer/reservation changes, OR consolidate into the existing stage-history tables.

### 2.8 Tests

| Test File | Coverage |
|-----------|----------|
| `tests/Feature/Crm/IndexTest.php` | CRM index, lead creation, task CRUD, note CRUD, org/contact creation |
| `tests/Feature/Crm/DealsTest.php` | Deal index, creation, stage movement, activity, show |

**Missing tests:**
- Lead update, assignment, stage change, duplicate detection
- Customer 360, customer creation
- Follow-up CRUD
- Site visit / meeting / call
- Offer creation, expiration, status
- Reservation conflicts, expiration
- Permissions / authorization
- Global search
- Mobile / PWA (not unit-testable, but can test routes render)

---

## 3. Existing Functionality Map

### 3.1 What Exists Today

- ✅ Dashboard CRM landing with quick forms
- ✅ Lead capture (public + dashboard)
- ✅ Lead list (paginated)
- ✅ Customer list (paginated)
- ✅ Tasks (polymorphic on lead/customer)
- ✅ Notes (polymorphic on lead/customer/deal/org/contact)
- ✅ Organizations + Contacts (B2B)
- ✅ Deals pipeline with stages
- ✅ Deal activities (call, email, meeting, note, task, whatsapp, sms, follow_up)
- ✅ Deal stage history
- ✅ Lead stage history
- ✅ Basic stats on dashboard
- ✅ Offer & Reservation migrations/models (controllers not inspected in CRM context)
- ✅ PWA, dark mode, RTL, Tailwind, Alpine

### 3.2 What Is Missing or Incomplete

| Feature | Status | Priority |
|---------|--------|----------|
| Lead priority, tags, whatsapp, campaign, unit interest | Missing | High |
| Lead source management (admin) | Missing | High |
| Lead assignment / reassignment / history | Missing | High |
| Lead stage pipeline (Kanban/list/mobile) | Partial (enum only, no board UI) | High |
| Customer 360 view | Missing | High |
| Unified activity timeline per customer/lead | Partial (only dashboard) | High |
| Follow-up system (create from lead/customer, complete, reschedule) | Missing | High |
| Call logging | Missing | High |
| WhatsApp shortcut / message templates | Missing | High |
| Meetings scheduling | Missing | High |
| Site visits module | Missing | High |
| Tags management | Missing | Medium |
| Document attachments | Missing | Medium |
| Global search | Partial (service exists, no UI/route) | High |
| CRM dashboard with charts/reports | Missing | High |
| Sales funnel / performance / lead source analytics | Missing | High |
| Calendar view | Missing | Medium |
| Mobile-first bottom nav + FAB quick actions | Missing | Critical |
| Duplicate lead detection | Missing | High |
| Fine-grained permissions / policies | Missing | High |
| Spatie Activity Log integration | Missing | Medium |
| Mobile date/time pickers | Missing | Medium |
| Click-to-call buttons | Missing | High |

---

## 4. Technical Debt

1. **`CrmController` is too large and mixed.** It handles dashboard, leads, tasks, notes, orgs, contacts. Should split into dedicated controllers.
2. **No `FormRequest` for tasks, notes, organizations, contacts, deals.** Validation lives in controllers.
3. **Tasks in `CrmController` only allow `lead`/`customer`, not `deal`/`project`/`unit`**, even though the `tasks` table is polymorphic.
4. **`LeadCreationService` stores `unit_id` in the notes text**, instead of a proper `lead_unit_interests` relationship.
5. **No `CrmActivity` linkage to `lead`/`customer` directly** — it is hard-tied to a `deal_id`.
6. **Offer and Reservation tables are not linked to `CrmDeal`.** Disconnects sales pipeline from actual offers/reservations.
7. **Global search service exists but is unused** in the CRM UI.
8. **Policies are not applied**; routes use role middleware.
9. **Spatie Activity Log is installed but unused**.
10. **No Redis cache usage for CRM data** despite Redis being available.
11. **`reports.index` likely empty** and not connected to CRM reporting.
12. **No PWA service worker file (`/sw.js`) was found in the codebase** (needs verification).
13. **PWA offline strategy may not cover CRM pages.**

---

## 5. Implementation Plan (Phase 2)

### Phase 2.1 — Foundation & Security (do first)

1. **Create fine-grained permissions and policies**
   - `LeadPolicy` with ownership: viewAny, view, create, update, delete, assign
   - `CustomerPolicy` with ownership
   - `CrmDealPolicy` with ownership
   - Register permissions: `view all leads`, `view team leads`, `view own leads`, `assign leads`, `manage crm`, etc.
   - Update `PermissionSeeder` and add migration-safe seeder logic.

2. **Refactor `CrmController` into smaller controllers**
   - `Crm\LeadController` (index, show, store, update, assign, stage)
   - `Crm\CustomerController` (index, show, store, update)
   - `Crm\TaskController`
   - `Crm\NoteController`
   - `Crm\OrganizationController`
   - `Crm\ContactController`
   - `Crm\FollowUpController`

3. **Add FormRequest classes**
   - `LeadRequest`, `CustomerRequest`, `TaskRequest`, `NoteRequest`, `OrganizationRequest`, `ContactRequest`, `FollowUpRequest`, `DealRequest`, `ActivityRequest`

### Phase 2.2 — Lead Management & Pipeline

1. **Extend `leads` table**
   - Add `whatsapp`, `priority`, `campaign`, `unit_type`, `bedrooms`, `required_area`, `preferred_payment_plan`, `status`

2. **Create `lead_sources` table + admin CRUD**
   - Dynamic sources, admin can disable/reorder

3. **Create `tags` table + polymorphic `taggables`**
   - Apply to `Lead` and `Customer`

4. **Create `lead_unit_interests` table**
   - track unit, interest_date, priority, notes, status

5. **Build Lead Kanban/List/Mobile views**
   - Board: `crm.leads.index` with stages from `LeadStage` enum
   - Drag/drop stage change via API
   - Stage history with user, reason, notes

6. **Lead assignment / reassignment**
   - `assigned_sales_id` update + `lead_assignment_histories` table

7. **Duplicate lead detection**
   - Warn on phone/whatsapp/email match before create

### Phase 2.3 — Customer 360

1. **Extend `customers` table**
   - Add `whatsapp`

2. **Create customer detail view**
   - Overview, timeline, leads, deals, interested units, offers, reservations, calls, meetings, site visits, tasks, notes, documents, activities

3. **Customer stats card**
   - Total deals, offers, reservations, value, last contact, next follow-up, assigned sales

4. **Customer timeline**
   - Aggregate calls, tasks, notes, offers, reservations, stage changes, follow-ups

### Phase 2.4 — Activities (Calls, WhatsApp, Meetings, Site Visits)

1. **Extend `crm_activities` to be top-level**
   - Add nullable `lead_id`, `customer_id` columns
   - Activities can now belong to deal OR lead OR customer

2. **Create dedicated controllers/views for:**
   - Calls
   - WhatsApp (shortcut, templates)
   - Meetings
   - Site Visits

3. **Use a shared `ActivityService` to record events** to the timeline.

### Phase 2.5 — Follow-ups & Tasks

1. **Build `FollowUpController` and views**
   - Dashboard widgets: today, overdue, upcoming
   - Complete/reschedule/cancel/reassign

2. **Upgrade tasks**
   - Support related `deal`, `project`, `unit`
   - Add reminder

### Phase 2.6 — Offers & Reservations

1. **Link offers/reservations to deals**
   - Add nullable `deal_id` FK

2. **Offer status lifecycle**
   - Draft → Generated → Sent → Accepted → Rejected → Expired → Cancelled

3. **Reservation conflict & expiration**
   - Unique active reservation per unit
   - Expiration cron/scope

### Phase 2.7 — CRM Dashboard, Reports & Analytics

1. **CRM Dashboard view**
   - New leads, qualified, follow-ups, site visits, open deals, offers, reservations, won, revenue

2. **Sales funnel report**
   - Conversion percentages between stages

3. **Sales performance report**
   - Per-sales-rep metrics

4. **Lead source analytics**
   - Revenue/conversion by source

### Phase 2.8 — Mobile-First UX

1. **Bottom navigation**
   - Dashboard, Leads, + (FAB), Tasks, More

2. **Quick action bottom sheet**
   - New Lead, New Customer, New Follow-up, New Task, New Offer, New Site Visit

3. **Mobile kanban**
   - Horizontal scroll? No — convert to stage cards/lists.

4. **Mobile forms**
   - Large touch targets, mobile date/time pickers, camera upload, file upload

5. **Click-to-call & WhatsApp**
   - `tel:` and `https://wa.me/` links

### Phase 2.9 — Global Search & Documents

1. **Global search route + controller**
   - Use existing `GlobalSearchService`, extend to leads, deals, contacts

2. **Document attachments**
   - Polymorphic `documents` table (or use Spatie Media Library if acceptable)
   - Private storage, signed URLs

### Phase 2.10 — Testing & Validation

1. Add PHPUnit tests for all new features
2. Run `php artisan test`
3. Run `vendor/bin/pint`
4. Run `php artisan migrate --pretend`
5. Verify PWA, RTL, dark mode, mobile

---

## 6. Migrations Plan

### New tables (only if missing)

1. `create_lead_sources_table.php`
2. `create_tags_table.php`
3. `create_taggables_table.php`
4. `create_lead_unit_interests_table.php`
5. `create_site_visits_table.php`
6. `create_meetings_table.php` OR `follow_ups` extension
7. `create_documents_table.php` OR Spatie Media Library
8. `create_lead_assignment_histories_table.php`

### Existing table extensions

1. `add_lead_enhancement_fields_to_leads_table.php`
2. `add_whatsapp_to_customers_table.php`
3. `add_deal_id_to_offers_table.php`
4. `add_deal_id_to_reservations_table.php`
5. `add_lead_customer_to_crm_activities_table.php`
6. `add_description_to_crm_stages_table.php`
7. `add_assigned_to_to_crm_organizations_table.php`

---

## 7. Files to Create / Modify (High Level)

### Controllers

- `app/Http/Controllers/Crm/LeadController.php`
- `app/Http/Controllers/Crm/CustomerController.php`
- `app/Http/Controllers/Crm/TaskController.php`
- `app/Http/Controllers/Crm/NoteController.php`
- `app/Http/Controllers/Crm/OrganizationController.php`
- `app/Http/Controllers/Crm/ContactController.php`
- `app/Http/Controllers/Crm/FollowUpController.php`
- `app/Http/Controllers/Crm/ActivityController.php`
- `app/Http/Controllers/Crm/SiteVisitController.php`
- `app/Http/Controllers/Crm/MeetingController.php`
- `app/Http/Controllers/Crm/ReportController.php`
- `app/Http/Controllers/Crm/CalendarController.php`
- `app/Http/Controllers/Crm/GlobalSearchController.php`
- `app/Http/Controllers/Crm/TagController.php`
- `app/Http/Controllers/Crm/LeadSourceController.php`

### Requests

- `app/Http/Requests/Crm/*`

### Models

- Extend `Lead`, `Customer`, `FollowUp`, `Task`, `Offer`, `Reservation`, `CrmActivity`, `CrmOrganization`, `CrmStage`
- Create `LeadSource`, `Tag`, `LeadUnitInterest`, `SiteVisit`, `Meeting`, `Document`, `LeadAssignmentHistory`

### Policies

- `app/Policies/CrmDealPolicy.php`
- Update `app/Policies/LeadPolicy.php`
- Update `app/Policies/CustomerPolicy.php`

### Views

- `resources/views/crm/leads/*`
- `resources/views/crm/customers/*`
- `resources/views/crm/follow-ups/*`
- `resources/views/crm/activities/*`
- `resources/views/crm/site-visits/*`
- `resources/views/crm/meetings/*`
- `resources/views/crm/reports/*`
- `resources/views/crm/calendar/*`
- Update `resources/views/crm/index.blade.php`
- Update `resources/views/dashboard/index.blade.php`

### Migrations

- See section 6.

### Tests

- `tests/Feature/Crm/LeadTest.php`
- `tests/Feature/Crm/CustomerTest.php`
- `tests/Feature/Crm/FollowUpTest.php`
- `tests/Feature/Crm/SiteVisitTest.php`
- `tests/Feature/Crm/MeetingTest.php`
- `tests/Feature/Crm/ReportTest.php`
- `tests/Feature/Crm/GlobalSearchTest.php`
- `tests/Feature/Crm/PermissionTest.php`

---

## 8. Security Checklist

- [ ] Enforce `can:` policy middleware on all CRM routes
- [ ] Validate file uploads (type, size, mime)
- [ ] Store documents in `storage/app/private` with signed URLs
- [ ] Rate-limit public inquiry endpoints
- [ ] CSRF on all forms
- [ ] XSS: escape all user output in Blade
- [ ] Audit log all critical changes
- [ ] Session security via existing Laravel config

---

## 9. Mobile-First Checklist

- [ ] Bottom navigation
- [ ] Floating action button with quick actions
- [ ] Mobile kanban / card lists
- [ ] Large touch targets (min 44px)
- [ ] Mobile date/time pickers
- [ ] Click-to-call and WhatsApp shortcuts
- [ ] Camera/file upload support
- [ ] PWA install and standalone mode
- [ ] Avoid horizontal scrolling
- [ ] Convert tables to mobile cards

---

## 10. Recommended Order of Implementation

Given the constraints and dependencies, the recommended order is:

1. **Security & Permissions** (foundation)
2. **Lead Management enhancements** (highest value, most used)
3. **Customer 360** (depends on lead data)
4. **Follow-ups & Tasks** (daily sales workflow)
5. **Calls, WhatsApp, Meetings, Site Visits** (activity timeline)
6. **Offers & Reservations integration** (links to deals)
7. **CRM Dashboard & Reports** (analytics)
8. **Global Search & Documents** (supporting features)
9. **Mobile-first navigation & quick actions** (UI/UX final layer)
10. **Tests, Pint, migrate --pretend, full validation**

---

## 11. What I Need From You

This is a very large scope. Before starting implementation, please confirm:

1. **Scope priority:** Should I start with Phase 2.1 (permissions + lead management) or is there a specific module you want first?
2. **Site visits / meetings:** Should these be separate tables or extensions of `follow_ups`/`crm_activities`?
3. **Documents:** Use a simple `documents` table or introduce Spatie Media Library?
4. **Activity log:** Use existing `Spatie Activity Log` or rely on the existing stage/activity history tables?
5. **Mobile-first scope:** Should the bottom navigation/FAB be implemented first, or after the backend features?

Once confirmed, I will proceed incrementally, one phase at a time, with tests at every step.
