# Implementation Plan for FluxHaven v1

## Module 0: Tenancy Foundation

### 0.1 Subdomain Routing
- **Objective**: Resolve the active tenant from the request subdomain.
- **Tasks**:
  - Register a subdomain route group in `routes/api.php` (e.g., `{tenant}.app.com`).
  - Create `TenantResolver` service to look up `Organization` by subdomain.
  - Bind resolved tenant to the request context (service container / request macro).

### 0.2 Per-Tenant Database Isolation
- **Objective**: Switch database connection per tenant on every request.
- **Tasks**:
  - Add per-tenant database connection entries to `config/database.php`.
  - Create `TenantDatabaseMiddleware` that calls `DB::purge()` and reconnects using tenant credentials.
  - Ensure migrations are scoped per tenant or use a shared schema with `organization_id` scoping (decide and document in spec).

### 0.3 Tenant Context Enforcement
- **Objective**: Prevent cross-tenant data leakage on all authenticated routes.
- **Tasks**:
  - Apply `TenantMiddleware` to all `api` route groups after authentication.
  - Add global Eloquent scope `BelongsToTenant` on all tenant-owned models.
  - Write integration tests verifying tenant A cannot read tenant B data.

## Module 1: Auth & Sanctum

### 1.1 CSRF & Sanctum Setup
- **Objective**: Configure Sanctum for SPA authentication with CSRF protection.
- **Tasks**:
  - Configure Sanctum in `config/sanctum.php`.
  - Set up CSRF token handling for API requests.
  - Define API token scopes and expiration.

### 1.2 Login/Register APIs
- **Objective**: Implement authentication endpoints.
- **Tasks**:
  - Create `AuthController` with registration, login, logout methods.
  - Implement `POST /api/v1/register` endpoint.
  - Implement `POST /api/v1/login` endpoint.
  - Implement `POST /api/v1/logout` endpoint.

### 1.3 User Profile Endpoint
- **Objective**: Create endpoint for authenticated user retrieval.
- **Tasks**:
  - Implement `GET /api/v1/me` endpoint.
  - Add Sanctum middleware protection.

### 1.4 Rate Limiting
- **Objective**: Prevent API abuse and ensure fair usage.
- **Tasks**:
  - Apply Laravel's `throttle` middleware to all `/api/v1/*` routes.
  - Set auth routes (login/register) to a stricter limit (e.g., 10 req/min).
  - Set general API routes to a standard limit (e.g., 60 req/min).

### 1.5 Content Security Policy
- **Objective**: Protect against XSS and injection attacks.
- **Tasks**:
  - Add CSP headers via middleware (or `spatie/laravel-csp`).
  - Configure policy to restrict script/style sources to trusted origins.
  - Apply middleware globally in `bootstrap/app.php`.

## Module 2: RBAC & Organization Management

### 2.1 Organizations Table
- **Objective**: Create database structure for organizations.
- **Tasks**:
  - Create `Organization` model with migration.
  - Define organization fields (id, name, subdomain, settings).

### 2.2 Roles & Permissions System
- **Objective**: Implement RBAC database structure.
- **Tasks**:
  - Create `Role` and `Permission` models with migrations.
  - Create pivot tables: `role_user`, `permission_role`.
  - Implement role assignment and permission checking logic.

### 2.3 Organization & User Management APIs
- **Objective**: Build CRUD endpoints for organizations and users.
- **Tasks**:
  - Create `OrganizationController` with CRUD methods.
  - Implement organization endpoints (POST, GET, PUT, DELETE).
  - Implement user management endpoints.

### 2.4 Role Management APIs
- **Objective**: Build endpoints for role and permission management.
- **Tasks**:
  - Create endpoints for role CRUD operations.
  - Implement `POST /api/v1/user-role-assignments` for role assignment.

### 2.5 RBAC Middleware
- **Objective**: Enforce role-based access control.
- **Tasks**:
  - Create `RoleMiddleware` for route protection.
  - Create `PermissionMiddleware` for granular access.
  - Apply middleware to protected routes.

### 2.6 Redis Caching
- **Objective**: Meet scalability and performance NFRs via caching.
- **Tasks**:
  - Configure Redis as the default cache driver in `.env` and `config/cache.php`.
  - Cache resolved user roles and permissions per request (invalidate on role change).
  - Cache frequently read data (organization settings, subscription plans) with appropriate TTLs.

## Module 3: Projects & Tasks

### 3.1 Projects Database & Model
- **Objective**: Create project data structure.
- **Tasks**:
  - Create `Project` model with migration.
  - Define project fields (id, name, description, status, owner_id).
  - Create project-user pivot table for team members.

### 3.2 Projects CRUD APIs
- **Objective**: Build project management endpoints.
- **Tasks**:
  - Create `ProjectController` with CRUD methods.
  - Implement `POST /api/v1/projects` endpoint.
  - Implement `GET /api/v1/projects` and `GET /api/v1/projects/{id}` endpoints.
  - Implement `PUT /api/v1/projects/{id}` and `DELETE /api/v1/projects/{id}` endpoints.

### 3.3 Tasks Database & Model
- **Objective**: Create task data structure.
- **Tasks**:
  - Create `Task` model with migration.
  - Define task fields (id, title, description, status, priority, due_date, project_id, assignee_id).

### 3.4 Tasks CRUD APIs
- **Objective**: Build task management endpoints.
- **Tasks**:
  - Create `TaskController` with CRUD methods.
  - Implement `POST /api/v1/tasks` endpoint.
  - Implement `GET /api/v1/tasks` and `GET /api/v1/tasks/{id}` endpoints.
  - Implement `PUT /api/v1/tasks/{id}` and `DELETE /api/v1/tasks/{id}` endpoints.

### 3.5 Task Filter Endpoints
- **Objective**: Implement task filtering by date/status.
- **Tasks**:
  - Implement `GET /api/v1/tasks/today` for today's tasks.
  - Implement `GET /api/v1/tasks/overdue` for overdue tasks.
  - Implement `GET /api/v1/tasks/upcoming` for upcoming tasks.

### 3.6 Subtasks Support
- **Objective**: Add subtask functionality to tasks.
- **Tasks**:
  - Create `Subtask` model with migration.
  - Create `SubtaskController` with CRUD methods.
  - Implement subtask endpoints under `/api/v1/tasks/{id}/subtasks`.

## Module 4: CRM

### 4.1 Contacts Database & Model
- **Objective**: Create contact management data structure.
- **Tasks**:
  - Create `Contact` model with migration.
  - Define contact fields (id, name, email, phone, organization_id).

### 4.2 Contacts CRUD APIs
- **Objective**: Build contact management endpoints.
- **Tasks**:
  - Create `ContactController` with CRUD methods.
  - Implement contact CRUD endpoints.

### 4.3 Deal Pipeline Database
- **Objective**: Create deal and pipeline stage data structure.
- **Tasks**:
  - Create `Deal` and `PipelineStage` models with migrations.
  - Define deal fields (id, title, value, stage_id, contact_id, owner_id).

### 4.4 Deals CRUD APIs
- **Objective**: Build deal management endpoints.
- **Tasks**:
  - Create `DealController` with CRUD methods.
  - Implement deal CRUD endpoints.

### 4.5 Pipeline Stage APIs
- **Objective**: Build pipeline stage management.
- **Tasks**:
  - Implement `POST /api/v1/stages` and `GET /api/v1/stages` endpoints.

### 4.6 CRM Lead Reports
- **Objective**: Generate lead tracking reports for CRM pipeline visibility.
- **Tasks**:
  - Create `ReportController` with a `leads` method.
  - Implement `GET /api/v1/reports/leads` returning deal counts and values grouped by stage.
  - Support query filters: `owner_id`, `from_date`, `to_date`.
  - Restrict to Manager and Admin roles via RBAC middleware.

## Module 5: Library & Subscriptions

### 5.1 Library Content Model
- **Objective**: Create library content data structure.
- **Tasks**:
  - Create `LibraryContent` model with migration.
  - Define content fields (id, title, type, description, access_level).

### 5.2 Subscription Plans
- **Objective**: Create subscription plan structure.
- **Tasks**:
  - Create `SubscriptionPlan` model with migration.
  - Define plan fields (id, name, tier, price, features).

### 5.3 User Subscriptions
- **Objective**: Track user subscription status.
- **Tasks**:
  - Create `UserSubscription` model with migration.
  - Link users to their active subscriptions.

### 5.4 Subscription Restriction Middleware
- **Objective**: Restrict content access by subscription tier.
- **Tasks**:
  - Create `SubscriptionMiddleware` to check access levels.
  - Apply middleware to premium content routes.

### 5.5 Content & Subscription APIs
- **Objective**: Build library and subscription endpoints.
- **Tasks**:
  - Create `LibraryController` for content CRUD.
  - Create `SubscriptionController` for subscription management.
  - Implement content and subscription CRUD endpoints.

## Module 6: LMS

### 6.1 Courses Database & Model
- **Objective**: Create course data structure.
- **Tasks**:
  - Create `Course` model with migration.
  - Define course fields (id, title, description, instructor_id, status).

### 6.2 Courses CRUD APIs
- **Objective**: Build course management endpoints.
- **Tasks**:
  - Create `CourseController` with CRUD methods.
  - Implement course CRUD endpoints.

### 6.3 Sections Database & Model
- **Objective**: Create course section structure.
- **Tasks**:
  - Create `Section` model with migration.
  - Define section fields (id, title, order, course_id).

### 6.4 Sections CRUD APIs
- **Objective**: Build section management endpoints.
- **Tasks**:
  - Create section CRUD endpoints under `/api/v1/sections`.

### 6.5 Lessons Database & Model
- **Objective**: Create lesson data structure.
- **Tasks**:
  - Create `Lesson` model with migration.
  - Define lesson fields (id, title, content, type, section_id, order).

### 6.6 Lessons CRUD APIs
- **Objective**: Build lesson management endpoints.
- **Tasks**:
  - Create lesson CRUD endpoints under `/api/v1/lessons`.

### 6.7 Enrollments
- **Objective**: Track user course enrollments and progress.
- **Tasks**:
  - Create `Enrollment` model with migration.
  - Define enrollment fields (id, user_id, course_id, progress, status).
  - Implement `POST /api/v1/enrollments` for enrollment.
  - Implement `GET /api/v1/enrollments` for listing enrollments.
  - Implement `GET /api/v1/enrollments/{id}` for retrieving a single enrollment.
  - Implement `PUT /api/v1/enrollments/{id}` for updating learner progress (progress %, status).

## Module 7: Frontend

### 7.1 Main Layout Component
- **Objective**: Create reusable layout for consistent UI.
- **Tasks**:
  - Create `MainLayout.vue` with header, sidebar, and footer.
  - Implement navigation components.
  - Add responsive design for mobile.

### 7.2 Authentication Pages
- **Objective**: Build login and registration UI.
- **Tasks**:
  - Create `Login.vue` page with form validation.
  - Create `Register.vue` page with form validation.
  - Wire up forms to authentication APIs using Inertia.

### 7.3 Dashboard Page
- **Objective**: Create main dashboard view.
- **Tasks**:
  - Create `Dashboard.vue` page.
  - Display user stats and quick actions.

### 7.4 Projects Pages
- **Objective**: Build project management UI.
- **Tasks**:
  - Create `Projects/Index.vue` for project listing.
  - Create `Projects/Show.vue` for project details.
  - Create `Projects/Create.vue` and `Projects/Edit.vue` forms.

### 7.5 Tasks Pages
- **Objective**: Build task management UI.
- **Tasks**:
  - Create `Tasks/Index.vue` for task listing with filters.
  - Create `Tasks/Show.vue` for task details with subtasks.
  - Create task forms with priority and assignment.

### 7.6 CRM Pages
- **Objective**: Build contacts and deals UI.
- **Tasks**:
  - Create `Contacts/Index.vue` and `Contacts/Show.vue` pages.
  - Create `Deals/Index.vue` with pipeline view.
  - Create deal stage management UI.

### 7.7 Library Pages
- **Objective**: Build library content browsing UI.
- **Tasks**:
  - Create `Library/Index.vue` with content filtering.
  - Create subscription plan selection UI.

### 7.8 LMS Pages
- **Objective**: Build course browsing and learning UI.
- **Tasks**:
  - Create `Courses/Index.vue` and `Courses/Show.vue` pages.
  - Create lesson viewer component.
  - Create enrollment management UI.

### 7.9 State Management
- **Objective**: Implement centralized state management.
- **Tasks**:
  - Set up Pinia stores for user, projects, tasks, CRM data.
  - Implement optimistic updates for common actions.
  - Handle loading states and errors.

### 7.10 Profile & Settings Page
- **Objective**: Allow users to view and update their own profile.
- **Tasks**:
  - Create `Profile/Show.vue` page displaying user info and role.
  - Create `Profile/Edit.vue` form for updating name, email, password.
  - Wire up to `GET /api/v1/me` and `PUT /api/v1/users/{id}` endpoints.

### 7.11 Frontend Performance
- **Objective**: Meet performance-first constitution mandate.
- **Tasks**:
  - Enable route-level lazy loading via dynamic `import()` in Vue Router.
  - Configure Vite code splitting for vendor and per-module chunks.
  - Audit and remove unused component imports.

## Module 8: Testing

### 8.1 Unit & Feature Tests (Pest)
- **Objective**: Achieve high test coverage per module per constitution mandate.
- **Tasks**:
  - Write Pest feature tests for all auth endpoints (register, login, logout, `/me`).
  - Write feature tests for RBAC middleware and role assignment.
  - Write feature tests for each CRUD module (Projects, Tasks, CRM, Library, LMS).
  - Write cross-tenant isolation tests verifying no data leakage between tenants.

### 8.2 End-to-End Tests (Cypress)
- **Objective**: Verify critical user flows end-to-end per constitution mandate.
- **Tasks**:
  - Install and configure Cypress in the project.
  - Write smoke tests for: login flow, project creation, task creation, course enrollment.
  - Integrate Cypress into CI pipeline.

### 8.3 Code Quality Tooling (PHPStan + ESLint)
- **Objective**: Enforce code quality standards per constitution mandate.
- **Tasks**:
  - Install and configure PHPStan at level 5+ (`phpstan.neon`).
  - Ensure ESLint v9 config covers all `.vue` and `.ts` files (`eslint.config.js`).
  - Add `composer analyse` and `npm run lint` as CI pipeline steps.
  - Fix any baseline violations before marking this task complete.

## Risks & Performance Considerations

### Risks
- **Sanctum Issues**: Regularly test Sanctum for token expiration, revocation, and refreshment.
- **Routing Conflicts**: Use Laravel's route naming conventions and middleware groups.
- **Multi-Tenancy Bugs**: Conduct thorough testing for data leakage between tenants.

### Performance Considerations
- **Caching**: Implement caching for frequently accessed data (user roles, permissions).
- **Database Indexing**: Ensure proper indexing on all foreign keys and filter columns.
- **Load Balancing**: Plan for horizontal scaling with load balancers as user base grows.

---

## Module 9: Public Landing Page + CMS (REQ-LAND-01 through REQ-LAND-09)

### 9.1 Landing Page Lead Capture ✅ done
- `LandingController@submit` (POST, rate-limited 5/min/IP, honeypot) creates Contact + Deal (stage: new).

### 9.2 CMS Backend + Shared Props ✅ done
- `SettingService::landingSettings()` / `updateLandingSettings()` — all CMS keys (hero_title, hero_subtitle, cta_label, about_text, footer_text, contact_email, contact_phone, contact_address, nav_links JSON, services JSON, careers JSON).
- `SettingController::landing()` + `updateLanding()` — GET/PUT `/admin/settings/landing`, `Cache::forget('settings:landing')` on save.
- `HandleInertiaRequests` shares `landing` prop (5-min TTL) to every page including public.

### 9.3 Admin CMS — Tabbed Editor + Live Preview ✅ done
- `Admin/Settings/Landing.vue` — page editor with 7 tabs (Hero, Navigation, Services, About, Careers, Contact, Footer).
- Split-panel live preview for Hero, Services, About, Careers tabs; Preview toggle in toolbar.
- `RichEditor.vue` — native contenteditable rich text component wired to heroSubtitle and aboutText.

### 9.4 CMS Page List + Per-Page Section Cards 🔲 next
- **Objective**: Replace tab bar with a navigable page list; each page has section cards with `enabled` toggle, `order`, and optional `link_to` cross-page links.
- **Tasks**:
  - Add `pages` key to `SettingService` storing `[{ slug, enabled, sections:[{id,title,enabled,order,link_to}] }]` as JSON.
  - Refactor `Admin/Settings/Landing.vue` to show page list as the default view.
  - Add per-page editor view (query param `?page=services` or sub-route) reusing existing section form fields.
  - Section card component with `link_to` optional field.
  - `SettingController::updateLandingPage()` — PATCH `/admin/settings/landing/{page}`.

### 9.5 Public Multi-Page Site 🔲 next
- **Objective**: Serve individual public pages driven by CMS data.
- **Tasks**:
  - Create `PublicLayout.vue` — top nav from `nav_links`, footer from `footer_text`, no hardcoded copy.
  - Create `resources/js/pages/Public/{Services,About,Careers,Contact,LibraryPreview}.vue`.
  - Create `PublicController` serving each route; pass relevant CMS slice as Inertia prop.
  - Register public routes in `routes/web.php` (no auth): `/services`, `/about`, `/careers`, `/contact`, `/library-preview`.
  - Sections with `link_to` render a "View more →" anchor on the page.
  - Home page renders teaser cards for Services, About, Careers, Contact — each clicking through to the full page.

---

## Module 10: ePub Library + FAQ (REQ-LIB-04, REQ-LIB-05, REQ-LIB-06)

### 10.1 ePub Upload & Storage
- **Objective**: Admin uploads ePub files; system stores securely and links to library item.
- **Tasks**:
  - Add `epub_path` nullable column to `library_items` (migration).
  - Update admin library CRUD to accept `.epub` file upload (max 50 MB); store in `storage/app/private/epubs/{uuid}.epub`.
  - Serve via signed URL route `GET /library/{item}/epub` (auth + subscription check).

### 10.2 In-Browser ePub Reader
- **Objective**: Subscribed users read ePubs in-browser without downloading.
- **Tasks**:
  - Install `epubjs` via npm (`npm install epubjs`).
  - Create `Library/Reader.vue` — full-screen dark reader with prev/next page, chapter TOC sidebar, font-size controls.
  - Reader fetches ePub via the signed URL and renders via `ePub()` / `rendition.display()`.
  - Save reading position (CFI string) to `user_library_progress` table (user_id, item_id, cfi, updated_at); restore on next open.
  - Route: `GET /library/{item}/read` → `LibraryController@read` → `Inertia::render('Library/Reader')`.

### 10.3 FAQ Section
- **Objective**: Admin manages FAQs; public FAQ page renders them grouped by category.
- **Tasks**:
  - Create `Faq` model + migration: `id, question, answer (text), category (string), sort_order (int), is_published (bool)`.
  - Seed a few example FAQs.
  - `Admin/Faq/Index.vue` — dark design system, CRUD list with inline edit + drag-to-reorder (sort_order).
  - Public `Faq/Index.vue` — accordion grouped by category, accessible on `/faq` (no auth).
  - Wire `FaqController` with admin routes (auth + permission `faqs.manage`) and public route.

---

## Module 11: LMS Roadmap Tasks + Verification (REQ-LMS-05 to REQ-LMS-08)

### 11.1 Roadmap-to-Course Scaffolding
- **Objective**: Admin pastes a roadmap; system creates sections + lessons automatically.
- **Tasks**:
  - Create `RoadmapParserService` — accepts JSON array `[{ section, lessons: [{ title, description }] }]` or structured Markdown; outputs `Section[]` + `Lesson[]`.
  - Add `Admin/Courses/ImportRoadmap.vue` — textarea for roadmap input, preview of parsed structure, confirm button.
  - `POST /admin/courses/{course}/import-roadmap` → parse → create sections + lessons in one DB transaction.
  - Return to course edit page with success toast.

### 11.2 Lesson Tasks
- **Objective**: Each lesson has tasks that learners must complete and submit.
- **Tasks**:
  - Create `lesson_tasks` table: `id, lesson_id, title, description (text), acceptance_criteria (text), due_days_after_enrollment (int nullable)`.
  - Create `lesson_task_submissions` table: `id, lesson_task_id, user_id, status (enum: pending|submitted|verified|rejected), submission_url (nullable), submission_note (text nullable), feedback (text nullable), submitted_at, reviewed_at, reviewer_id`.
  - Create `LessonTask` + `LessonTaskSubmission` models with relationships.
  - Admin UI: task list on the lesson edit page (add/edit/delete tasks per lesson).

### 11.3 Task Assignment on Enrollment
- **Objective**: When a learner enrolls in a course, tasks for all lessons are auto-assigned.
- **Tasks**:
  - In `EnrollmentObserver` (or `enrolled` event listener): loop course lessons → create `LessonTaskSubmission` rows (status: pending) for each task, computing due date from `due_days_after_enrollment`.
  - Learner's task list shows tasks from their enrollments, grouped by course/lesson.

### 11.4 Task Verification Workflow
- **Objective**: Learner submits → tutor reviews → admin can override.
- **Tasks**:
  - Learner: `PATCH /my/task-submissions/{submission}` — set `submission_url`, `submission_note`, `status: submitted`.
  - Tutor: `PATCH /tutor/task-submissions/{submission}` — set `status: verified|rejected`, `feedback`.
  - Admin/subadmin with `tasks.verify` permission: same endpoint; Gate bypasses tutor-only restriction.
  - `TutorDashboard.vue` — lists all submissions for courses the tutor is assigned to, filterable by status.

### 11.5 Tutor Assignment
- **Objective**: Admin assigns tutors to courses or individual enrollments.
- **Tasks**:
  - Create `course_tutor` pivot: `course_id, user_id (tutor), assigned_by`.
  - Admin `Courses/Show.vue` panel: "Tutors" tab — assign/remove tutors (users with `tutor` role).
  - Tutor sees only courses + submissions they're assigned to (Gate policy on `TutorDashboard`).
