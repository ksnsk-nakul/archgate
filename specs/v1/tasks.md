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

---

## ✅ Module 1: Auth & Sanctum

### Day 1: Setup Authentication Core

#### Task 1.1: Install & Configure Sanctum

- Install Sanctum
- Publish config
- Configure `.env` (SESSION_DOMAIN, SANCTUM_STATEFUL_DOMAINS)
- Enable middleware in Kernel

#### Task 1.2: CSRF Setup

- Enable CSRF cookie endpoint
- Test with Postman / frontend

---

### Day 2: Auth APIs

#### Task 1.3: Register API

- Validate inputs
- Create user
- Return token

#### Task 1.4: Login API

- Authenticate user
- Issue token

#### Task 1.5: Logout API

- Revoke token

#### Task 1.6: `/me` Endpoint

- Return user + role + org

---

## ✅ Module 2: RBAC & Organization

### Day 3: Database Design

#### Task 2.1: Organizations Table

#### Task 2.2: Roles Table

#### Task 2.3: Permissions Table

#### Task 2.4: Pivot Tables

- role_user
- permission_role

---

### Day 4: Core APIs

#### Task 2.5: Organization CRUD

#### Task 2.6: User CRUD

---

### Day 5: RBAC Logic

#### Task 2.7: Role CRUD

#### Task 2.8: Assign Roles to Users

#### Task 2.9: RBAC Middleware

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
