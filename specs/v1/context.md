# FluxHaven v1 — Session Context

> Load this file at the start of any new session to restore full project context.

---

## Project Overview

**FluxHaven v1** is a multi-tenant SaaS built with Laravel 13 + Inertia.js v3 + Vue 3.  
Goal: RBAC, Projects/Tasks, CRM, Library, and LMS modules — all scoped per organization.

---

## Repo & Environment

| Item | Value |
|------|-------|
| Main project path | `/Users/nakul/Herd/archgate` |
| Active worktree | `/Users/nakul/Herd/archgate/.claude/worktrees/nifty-dirac-e9320f` |
| Active branch | `claude/nifty-dirac-e9320f` |
| Base branch | `main` |
| App URL (Herd) | `https://archgate.test` |
| PHP | 8.4 |
| Laravel | 13 |
| Test runner | Pest 4 (SQLite in-memory) |

**Worktree setup notes:**
- The worktree has its own `vendor/` installed via `composer install` (not symlinked from main).
- `.env` was copied from the main project. DB_CONNECTION=sqlite uses a file path for local dev;  
  tests use in-memory SQLite via `phpunit.xml`.
- Run tests from the worktree root: `./vendor/bin/pest tests/Feature/RbacTest.php --no-coverage`
- Run `vendor/bin/pint --dirty --format agent` after any PHP changes.

---

## Specs Location

All specs live in `/specs/v1/`:

| File | Purpose |
|------|---------|
| `specification.md` | Source of truth — functional requirements |
| `plan.md` | Architecture & module breakdown |
| `tasks.md` | Day-by-day task list (Modules 1–7) |
| `context.md` | This file — session restoration |

---

## Completed Work

### Module 1 — Auth & Sanctum ✅

- `app/Http/Controllers/AuthController.php` — register, login, logout, me
- Routes wired in `routes/api.php` under `/api/v1/`
- Sanctum installed; `auth:sanctum` middleware used on protected routes

### Module 2 — RBAC & Organization (Day 3 complete) ✅

**Models created:**
- `app/Models/Organization.php` — subdomain-based tenancy
- `app/Models/Role.php` — slug-based roles with permissions relationship
- `app/Models/Permission.php` — slug-based permissions
- `app/Models/User.php` — extended with `roles()`, `organizations()`, `hasRole()`, `hasPermission()`

**Migrations created:**
- `2026_04_29_000001_create_organizations_table.php`
- `2026_04_29_000002_create_roles_table.php`
- `2026_04_29_000003_create_permissions_table.php`
- `2026_04_29_000004_create_rbac_pivot_tables.php` — `role_user` (with `organization_id`) + `permission_role`

**Factories created:**
- `OrganizationFactory`, `RoleFactory` (states: admin, manager, member), `PermissionFactory`

**Tests passing:**
- `tests/Feature/RbacTest.php` — 6/6 tests green

---

## API Routes (all defined, controllers pending)

All routes are in `routes/api.php` under `Route::prefix('v1')`:

```
POST   /api/v1/register
POST   /api/v1/login
POST   /api/v1/logout             [auth:sanctum]
GET    /api/v1/me                 [auth:sanctum]

apiResource users                 [auth:sanctum]  → UserController
apiResource organizations         [auth:sanctum]  → OrganizationController
apiResource roles                 [auth:sanctum]  → RoleController
POST   user-role-assignments      [auth:sanctum]  → UserRoleController@store

apiResource projects              [auth:sanctum]  → ProjectController
GET    tasks/today|overdue|upcoming               → TaskController
apiResource tasks                 [auth:sanctum]  → TaskController
apiResource tasks.subtasks (shallow)              → SubtaskController

apiResource contacts              [auth:sanctum]  → ContactController
apiResource deals                 [auth:sanctum]  → DealController
apiResource stages (index,store)  [auth:sanctum]  → PipelineStageController

apiResource content               [auth:sanctum]  → LibraryContentController
apiResource subscriptions         [auth:sanctum]  → SubscriptionController

apiResource courses               [auth:sanctum]  → CourseController
apiResource sections              [auth:sanctum]  → SectionController
apiResource lessons               [auth:sanctum]  → LessonController
apiResource enrollments (index,show,store)        → EnrollmentController
```

---

## Next Steps (pick up here)

**Day 4 — Task 2.5 & 2.6: Organization CRUD + User CRUD**

Create:
- `app/Http/Controllers/OrganizationController.php` — full apiResource CRUD
- `app/Http/Controllers/UserController.php` — full apiResource CRUD
- Corresponding Form Requests for validation
- Feature tests for both controllers

**Day 5 — Task 2.7, 2.8, 2.9: Role CRUD + Role Assignment + RBAC Middleware**

Create:
- `app/Http/Controllers/RoleController.php`
- `app/Http/Controllers/UserRoleController.php`
- RBAC middleware (`RoleMiddleware`, `PermissionMiddleware`)

---

## Key Technical Notes

### Do NOT use `wherePivot()` — it generates broken SQL in this stack

The `wherePivot()` method produces `"pivot" = 'column_name'` instead of `"table"."column" = value`.  
Always use `where('table.column', $value)` for pivot conditions:

```php
// WRONG
->wherePivot('organization_id', $orgId)

// CORRECT
->where('role_user.organization_id', $orgId)
```

### RBAC method signatures on User model

```php
$user->hasRole('admin', $organizationId)        // bool
$user->hasPermission('tasks.view', $organizationId)  // bool
```

### Test pattern

```php
uses(LazilyRefreshDatabase::class);  // at top of test file

test('...', function () {
    $user = User::factory()->create();
    $org  = Organization::factory()->create();
    $role = Role::factory()->admin()->create();
    $user->roles()->attach($role->id, ['organization_id' => $org->id]);
    expect($user->hasRole('admin', $org->id))->toBeTrue();
});
```

### Pint (mandatory after PHP changes)

```bash
vendor/bin/pint --dirty --format agent
```

### Running tests

```bash
php artisan test --compact --filter=RbacTest
# or
./vendor/bin/pest tests/Feature/RbacTest.php --no-coverage
```

---

## Auto-Snapshot — 2026-04-29 05:23 (branch: claude/nifty-dirac-e9320f)

**Models:** Organization,Permission,Role,User,

**Controllers:** AuthController,

**Feature Tests:** DashboardTest,ExampleTest,RbacTest,

**Migrations (2026):**
  - 2026_04_28_145124_create_personal_access_tokens_table
  - 2026_04_29_000001_create_organizations_table
  - 2026_04_29_000002_create_roles_table
  - 2026_04_29_000003_create_permissions_table
  - 2026_04_29_000004_create_rbac_pivot_tables

**Git status:**
```
 M app/Models/User.php
?? .claude/scripts/
?? app/Models/Organization.php
?? app/Models/Permission.php
?? app/Models/Role.php
?? database/factories/OrganizationFactory.php
?? database/factories/PermissionFactory.php
?? database/factories/RoleFactory.php
?? database/migrations/2026_04_29_000001_create_organizations_table.php
?? database/migrations/2026_04_29_000002_create_roles_table.php
?? database/migrations/2026_04_29_000003_create_permissions_table.php
?? database/migrations/2026_04_29_000004_create_rbac_pivot_tables.php
?? specs/v1/context.md
?? tests/Feature/RbacTest.php
```
