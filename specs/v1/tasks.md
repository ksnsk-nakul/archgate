# FluxHaven v1 Tasks (Refactored for Daily Progress)

---

## ✅ Module 0: Tenancy Foundation

### Day 0: Tenancy Setup

#### Task 0.1: Subdomain Routing Middleware

- Register subdomain route group (`{tenant}.app.com`)
- Create `TenantResolver` service to look up `Organization` by subdomain
- Bind resolved tenant to request context

#### Task 0.2: Per-Tenant Database Isolation

- Configure per-tenant DB connections in `config/database.php`
- Create `TenantDatabaseMiddleware` (purge + reconnect on each request)
- Document schema strategy: isolated DB vs shared DB with `organization_id` scoping

#### Task 0.3: Tenant Context Enforcement

- Apply `TenantMiddleware` to all authenticated API route groups
- Add global Eloquent scope `BelongsToTenant` to all tenant-owned models
- Write cross-tenant isolation integration tests

#### Task 0.4: Code Quality Tooling Setup

- Install PHPStan (`phpstan/phpstan-laravel`) and configure `phpstan.neon` at level 5
- Verify ESLint v9 config (`eslint.config.js`) covers all `.vue` and `.ts` files
- Add `composer analyse` and `npm run lint` scripts to CI pipeline
- Resolve all baseline violations before implementation begins

---

## ✅ Module 1: Auth & Sanctum

### Day 1: Setup Authentication Core

#### [X] Task 1.1: Install & Configure Sanctum

- Install Sanctum
- Publish config
- Configure `.env` (SESSION_DOMAIN, SANCTUM_STATEFUL_DOMAINS)
- Enable middleware in Kernel
- Set token expiry TTL in `config/sanctum.php` (`expiration` key, e.g., 1440 minutes)

#### [X] Task 1.2: CSRF Setup

- Enable CSRF cookie endpoint
- Test with Postman / frontend

---

### Day 2: Auth APIs

#### [X] Task 1.3: Register API

- Validate inputs
- Create user
- Return token

#### [X] Task 1.4: Login API

- Authenticate user
- Issue token

#### [X] Task 1.5: Logout API

- Revoke token

#### [X] Task 1.6: `/me` Endpoint

- Return user + role + org

#### [X] Task 1.7: Rate Limiting Middleware

- Apply `throttle:10,1` to auth routes (login, register)
- Apply `throttle:60,1` to all other `/api/v1/*` routes
- Return `429 Too Many Requests` with `Retry-After` header on breach

#### [X] Task 1.8: Content Security Policy Middleware

- Create `CspMiddleware` (or use `spatie/laravel-csp`) to set CSP headers
- Restrict `script-src` and `style-src` to trusted origins
- Register middleware globally in `bootstrap/app.php`

---

## ✅ Module 2: RBAC & Organization

### Day 3: Database Design

#### [X] Task 2.1: Organizations Table

#### [X] Task 2.2: Roles Table

#### [X] Task 2.3: Permissions Table

#### [X] Task 2.4: Pivot Tables

- role_user
- permission_role

---

### Day 4: Core APIs

#### [X] Task 2.5: Organization CRUD

#### [X] Task 2.6: User CRUD

---

### Day 5: RBAC Logic

#### [X] Task 2.7: Role CRUD

#### [X] Task 2.8: Assign Roles to Users

#### [X] Task 2.9: RBAC Middleware

#### [X] Task 2.10: Redis Caching Setup

- Set `CACHE_DRIVER=redis` in `.env`
- Configure Redis connection in `config/database.php`
- Cache resolved user roles/permissions per user ID (invalidate on role change)
- Cache organization settings and subscription plans with appropriate TTLs

---

## ✅ Module 3: Projects & Tasks

### Day 6: Projects

#### Task 3.1: Projects Migration + Model

#### Task 3.2: Projects CRUD API

---

### Day 7: Tasks

#### Task 3.3: Tasks Migration + Model

#### Task 3.4: Tasks CRUD API

---

### Day 8: Advanced Task Features

#### Task 3.5: Task Filters

- Today
- Overdue
- Upcoming

#### Task 3.6: Subtasks Model

#### Task 3.7: Subtasks CRUD

#### Task 3.8: Task Deadline Reminders

- Create `SendTaskDeadlineReminder` queued notification (Laravel Notifications)
- Schedule daily job to dispatch reminders for tasks due within 24 hours
- Notify assigned user via database channel (email channel optional for v1)
- Register scheduled command in `routes/console.php`

---

## ✅ Module 4: CRM

### Day 9: Contacts

#### Task 4.1: Contacts Table

#### Task 4.2: Contacts CRUD

---

### Day 10: Deals

#### Task 4.3: Deals + Stages Tables

#### Task 4.4: Deals CRUD

#### Task 4.5: Pipeline Stages API

#### Task 4.6: CRM Lead Report API

- Implement `GET /api/v1/reports/leads`
- Aggregate deals by pipeline stage, owner, and date range
- Return summary counts and deal values per stage
- Apply RBAC: Manager and Admin only

---

## ✅ Module 5: Library

### Day 11: Content System

#### Task 5.1: Library Content Table

#### Task 5.2: Content CRUD

---

### Day 12: Subscription System

#### Task 5.3: Subscription Plans

#### Task 5.4: User Subscriptions

#### Task 5.5: Subscription Middleware

#### Task 5.6: Subscription APIs

---

## ✅ Module 6: LMS

### Day 13: Courses

#### Task 6.1: Courses Table

#### Task 6.2: Courses CRUD

---

### Day 14: Course Structure

#### Task 6.3: Sections

#### Task 6.4: Sections CRUD

---

### Day 15: Lessons

#### Task 6.5: Lessons Table

#### Task 6.6: Lessons CRUD

---

### Day 16: Enrollments

#### Task 6.7: Enrollment Table

#### Task 6.8: Enrollment APIs

- `POST /api/v1/enrollments` — enroll user in course
- `GET /api/v1/enrollments` — list enrollments (scoped to tenant)
- `GET /api/v1/enrollments/{id}` — retrieve single enrollment

#### Task 6.9: Enrollment Progress Update

- `PUT /api/v1/enrollments/{id}` — update `progress` (0–100) and `status` fields
- Validate progress is between 0 and 100
- Auto-set status to `completed` when progress reaches 100

---

## ✅ Module 7: Frontend (Inertia + Vue)

### Day 17: Layout & Auth UI

#### Task 7.1: Layout

#### Task 7.2: Login/Register Pages

---

### Day 18: Dashboard

#### Task 7.3: Dashboard Page

---

### Day 19: Projects UI

#### Task 7.4: Project Pages

---

### Day 20: Tasks UI

#### Task 7.5: Task Pages + Filters

---

### Day 21: CRM UI

#### Task 7.6: Contacts + Deals UI

---

### Day 22: Library UI

#### Task 7.7: Library Pages

---

### Day 23: LMS UI

#### Task 7.8: Courses + Lessons UI

---

### Day 24: State Management

#### Task 7.9: Pinia Stores

---

### Day 25: Profile, Settings & Performance

#### Task 7.10: Profile & Settings Page

- Create `Profile/Show.vue` displaying user info and role
- Create `Profile/Edit.vue` form for name, email, password update
- Wire up to `GET /api/v1/me` and `PUT /api/v1/users/{id}`

#### Task 7.11: Frontend Performance Optimisation

- Enable route-level lazy loading via dynamic `import()` in Vue Router
- Configure Vite code splitting for vendor and per-module chunks
- Verify Vite build output has no single chunk exceeding 500KB

---

## ✅ Module 8: Testing

### Day 25: Auth, RBAC & Tenancy Tests

#### Task 8.1: Auth Tests

- Pest feature tests for register, login, logout, `/me`
- Test token issuance and revocation

#### Task 8.2: RBAC & Tenancy Tests

- Unit tests for Role/Permission models
- Feature tests for role assignment and permission middleware
- Cross-tenant isolation tests (tenant A cannot access tenant B data)

---

### Day 26: Module Feature Tests

#### Task 8.3: Project & Task Tests

- Feature tests for project/task CRUD
- Test task filters (today, overdue, upcoming)
- Test subtask creation and listing

#### Task 8.4: CRM, Library & LMS Tests

- Feature tests for contacts, deals, pipeline stages
- Subscription access restriction tests (free vs premium content)
- Enrollment and progress tracking tests

---

### Day 27: E2E Tests

#### Task 8.5: Cypress Setup & Smoke Tests

- Install and configure Cypress
- Write smoke tests for critical user flows: login, create project, create task, enroll in course
- Add Cypress run to CI pipeline (`npm run cypress:ci`)

---

## ✅ Module 9: Public Landing Page + CMS

### REQ-CRM-04 → TASK-9.1: Lead Capture Form (Public) [✅ done]
- `LandingController@submit` (POST, rate-limited 5/min/IP, honeypot)
- Creates `Contact` + `Deal` (stage: new) from form submission

### REQ-CRM-05, REQ-LAND-02a–02d, REQ-LAND-08, REQ-LAND-09 → TASK-9.2: Landing CMS — Page List + Per-Page Editors [✅ done]
- `SettingService::landingSettings()` / `updateLandingSettings()` — stores all CMS fields (hero_title, hero_subtitle, cta_label, about_text, footer_text, contact_email, contact_phone, contact_address, nav_links JSON, services JSON, careers JSON)
- `SettingController::landing()` + `updateLanding()` — GET/PUT `/admin/settings/landing`, cache invalidation on save
- `Admin/Settings/Landing.vue` — tabbed page editor (Hero, Navigation, Services, About, Careers, Contact, Footer) with live split-panel preview for Hero, Services, About, Careers tabs; Preview toggle in toolbar
- `RichEditor.vue` — native contenteditable rich text component (Bold/Italic/Underline/Lists/H2/H3/Paragraph/Clear) wired to heroSubtitle and aboutText via v-model
- `HandleInertiaRequests` shares `landing` prop (5-min cached) to all pages including public Welcome.vue

### REQ-LAND-01, REQ-LAND-03 → TASK-9.3: Public Landing Pages + Shared Layout [🔲 next]
- Create `PublicLayout.vue` with top nav (from `nav_links` CMS) + footer (from `footer_text` CMS)
- Create individual public Inertia pages: `/services`, `/about`, `/careers`, `/contact`, `/library-preview`
- Each page is a separate Vue component under `resources/js/pages/Public/`
- `PublicController` serves each route, passing relevant CMS slice as Inertia prop
- Routes registered in `routes/web.php` (no auth middleware)

### REQ-LAND-02b, REQ-LAND-02c → TASK-9.4: CMS Page List View + Section Cards [🔲 next]
- Refactor `Admin/Settings/Landing.vue` to show a **page list** as the default view (replaces tab bar)
- Page list rows: Home (always enabled), Services, About, Careers, Contact, Library Preview, Footer/Nav — each with enabled toggle + "Edit page" link
- Clicking "Edit page" navigates to the per-page section editor (same URL with `?page=services` query param or separate sub-route)
- Section cards within each page: title, content fields, `enabled` toggle, `order`, optional `link_to` field
- `link_to` renders a "View more →" CTA on the public page linking to the target route
- Persist page-level `enabled` state and section `order`/`link_to` to `SettingService` as JSON

### REQ-LAND-04 → TASK-9.5: Library Preview Public Page [🔲 pending]
- Public `/library-preview` lists `LibraryItem` records (title, cover, type) — no auth
- "Read" / "Buy" buttons redirect to `/login?intended=/library/{id}/read`

### REQ-LAND-05 → TASK-9.6: Lead Form Integration [🔲 pending]
- Embed lead capture form on Home and Contact public pages
- Wire to `LandingController@submit`

---

## 🔲 Module 10: ePub Library + FAQ

### REQ-LIB-04 → TASK-10.1: ePub Upload
- Add `epub_path` nullable column to `library_items` via migration
- Update admin library CRUD to accept `.epub` upload (max 50 MB), store in `private/epubs/{uuid}.epub`
- Serve via signed URL: `GET /library/{item}/epub` (auth + subscription gate)

### REQ-LIB-05 → TASK-10.2: In-Browser ePub Reader
- `npm install epubjs`
- Create `Library/Reader.vue` — full-screen dark reader with prev/next, TOC sidebar, font-size toggle
- Fetch ePub via signed URL, render with `ePub()` / `rendition.display()`
- Create `user_library_progress` table: `user_id, item_id, cfi, updated_at` — save/restore reading position
- Route: `GET /library/{item}/read` → `LibraryController@read`

### REQ-LIB-06 → TASK-10.3: FAQ Section
- Create `Faq` model + migration: `question, answer, category, sort_order, is_published`
- Seed 5–8 example FAQs
- `Admin/Faq/Index.vue` — dark design CRUD with inline edit + sort order control
- Public `Faq/Index.vue` — accordion grouped by category, route `GET /faq` (no auth)
- Wire `FaqController` with admin + public routes; permission gate `faqs.manage` for admin actions

---

## 🔲 Module 11: LMS Roadmap Tasks + Verification

### REQ-LMS-05 → TASK-11.1: Roadmap-to-Course Import
- Create `RoadmapParserService` — parses JSON `[{ section, lessons:[{title,description}] }]`
- `Admin/Courses/ImportRoadmap.vue` — textarea input, parsed preview, confirm to scaffold
- `POST /admin/courses/{course}/import-roadmap` → transaction creates sections + lessons
- Return to course edit page with toast

### REQ-LMS-06 → TASK-11.2: Lesson Tasks CRUD
- Migration: `lesson_tasks` (id, lesson_id, title, description, acceptance_criteria, due_days_after_enrollment)
- Migration: `lesson_task_submissions` (id, lesson_task_id, user_id, status enum, submission_url, submission_note, feedback, submitted_at, reviewed_at, reviewer_id)
- `LessonTask` + `LessonTaskSubmission` models with relationships
- Admin UI: task list on lesson edit page (add/edit/delete)

### REQ-LMS-07 → TASK-11.3: Task Assignment on Enrollment
- `EnrollmentObserver` (or listener): on enrollment created, loop lessons → create `LessonTaskSubmission` rows (status: pending) with computed due dates
- Learner task list shows assigned tasks grouped by course/lesson

### REQ-LMS-07 → TASK-11.4: Submission & Verification Workflow
- Learner: `PATCH /my/task-submissions/{submission}` — submit with url + note
- Tutor: `PATCH /tutor/task-submissions/{submission}` — verify or reject with feedback
- Admin override via `tasks.verify` permission (Gate bypass)
- Policy ensures only assigned tutor or admin can review

### REQ-LMS-08 → TASK-11.5: Tutor Dashboard
- Create `course_tutor` pivot: `course_id, user_id, assigned_by`
- `TutorDashboard.vue` — lists pending submissions for assigned courses, filter by status/course
- Admin: "Tutors" tab on course page — assign/remove users with `tutor` role

### REQ-LMS-08 → TASK-11.6: Seed Permissions for New Modules
- Add permissions: `faqs.manage`, `landing.manage`, `tasks.verify`, `courses.import-roadmap`
- Update `RbacSeeder` to include these under correct groups
- Update permission group icons in `Permissions.vue` groupIcon map
