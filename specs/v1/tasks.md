# FluxHaven v1 Tasks

## Module 1: Auth & Sanctum

### Task 1.1: Sanctum & CSRF Setup
- **Description**: Configure Laravel Sanctum for SPA authentication with CSRF protection.
- **Related Files**: `config/sanctum.php`, `.env`, `app/Http/Kernel.php`
- **API Endpoints**: N/A
- **Expected Output**: Sanctum configured with CSRF token generation for API requests.

### Task 1.2: Login/Register APIs
- **Description**: Create API endpoints for user registration and login.
- **Related Files**: `app/Http/Controllers/AuthController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/register
  - POST /api/v1/login
  - POST /api/v1/logout
- **Expected Output**: Users can register, login, and logout with Sanctum tokens.

### Task 1.3: /api/v1/me Endpoint
- **Description**: Create endpoint to retrieve the currently authenticated user.
- **Related Files**: `app/Http/Controllers/AuthController.php`, `routes/api.php`
- **API Endpoints**: GET /api/v1/me
- **Expected Output**: Returns authenticated user details with role and organization info.

---

## Module 2: RBAC & Organization Management

### Task 2.1: Organizations Table
- **Description**: Create database table for organizations with subdomain support.
- **Related Files**: `database/migrations/*_create_organizations_table.php`, `app/Models/Organization.php`
- **API Endpoints**: N/A
- **Expected Output**: Organizations table with id, name, subdomain, settings fields.

### Task 2.2: Roles & Permissions Tables
- **Description**: Create database tables for roles and permissions system.
- **Related Files**: 
  - `database/migrations/*_create_roles_table.php`
  - `database/migrations/*_create_permissions_table.php`
  - `database/migrations/*_create_role_user_table.php`
  - `database/migrations/*_create_role_permission_table.php`
- **API Endpoints**: N/A
- **Expected Output**: Complete RBAC database structure with pivot tables.

### Task 2.3: Organization CRUD APIs
- **Description**: Create API endpoints for organization management.
- **Related Files**: `app/Http/Controllers/OrganizationController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/organizations
  - GET /api/v1/organizations/{id}
  - PUT /api/v1/organizations/{id}
  - DELETE /api/v1/organizations/{id}
- **Expected Output**: Full CRUD operations for organizations.

### Task 2.4: User Management APIs
- **Description**: Create API endpoints for user CRUD operations.
- **Related Files**: `app/Http/Controllers/UserController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/users
  - GET /api/v1/users/{id}
  - PUT /api/v1/users/{id}
  - DELETE /api/v1/users/{id}
- **Expected Output**: Full CRUD operations for users within organizations.

### Task 2.5: Role Management APIs
- **Description**: Create API endpoints for role and permission management.
- **Related Files**: `app/Http/Controllers/RoleController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/roles
  - GET /api/v1/roles/{id}
  - PUT /api/v1/roles/{id}
  - DELETE /api/v1/roles/{id}
- **Expected Output**: Full CRUD operations for roles and permissions.

### Task 2.6: User Role Assignment API
- **Description**: Create endpoint to assign roles to users.
- **Related Files**: `app/Http/Controllers/RoleController.php`, `routes/api.php`
- **API Endpoints**: POST /api/v1/user-role-assignments
- **Expected Output**: Users can be assigned roles within organizations.

### Task 2.7: RBAC Middleware
- **Description**: Create middleware to enforce role-based access control.
- **Related Files**: `app/Http/Middleware/RoleMiddleware.php`, `app/Http/Middleware/PermissionMiddleware.php`
- **API Endpoints**: N/A
- **Expected Output**: Middleware that checks user roles/permissions before allowing route access.

---

## Module 3: Projects & Tasks

### Task 3.1: Projects Database & Model
- **Description**: Create Project model and migration with team member relationships.
- **Related Files**: `database/migrations/*_create_projects_table.php`, `app/Models/Project.php`
- **API Endpoints**: N/A
- **Expected Output**: Projects table with id, name, description, status, owner_id fields.

### Task 3.2: Projects CRUD APIs
- **Description**: Create API endpoints for project CRUD operations.
- **Related Files**: `app/Http/Controllers/ProjectController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/projects
  - GET /api/v1/projects
  - GET /api/v1/projects/{id}
  - PUT /api/v1/projects/{id}
  - DELETE /api/v1/projects/{id}
- **Expected Output**: Full CRUD operations for projects.

### Task 3.3: Tasks Database & Model
- **Description**: Create Task model and migration with assignee and project relationships.
- **Related Files**: `database/migrations/*_create_tasks_table.php`, `app/Models/Task.php`
- **API Endpoints**: N/A
- **Expected Output**: Tasks table with id, title, description, status, priority, due_date, project_id, assignee_id.

### Task 3.4: Tasks CRUD APIs
- **Description**: Create API endpoints for task CRUD operations.
- **Related Files**: `app/Http/Controllers/TaskController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/tasks
  - GET /api/v1/tasks
  - GET /api/v1/tasks/{id}
  - PUT /api/v1/tasks/{id}
  - DELETE /api/v1/tasks/{id}
- **Expected Output**: Full CRUD operations for tasks.

### Task 3.5: Task Filter Endpoints
- **Description**: Implement filters for today, overdue, and upcoming tasks.
- **Related Files**: `app/Http/Controllers/TaskController.php`, `routes/api.php`
- **API Endpoints**:
  - GET /api/v1/tasks/today
  - GET /api/v1/tasks/overdue
  - GET /api/v1/tasks/upcoming
- **Expected Output**: Filtered task lists based on due date criteria.

### Task 3.6: Subtasks Database & Model
- **Description**: Create Subtask model and migration with parent task relationship.
- **Related Files**: `database/migrations/*_create_subtasks_table.php`, `app/Models/Subtask.php`
- **API Endpoints**: N/A
- **Expected Output**: Subtasks table with id, title, status, parent_task_id fields.

### Task 3.7: Subtasks CRUD APIs
- **Description**: Create API endpoints for subtask CRUD operations.
- **Related Files**: `app/Http/Controllers/SubtaskController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/tasks/{id}/subtasks
  - GET /api/v1/subtasks
  - GET /api/v1/subtasks/{id}
  - PUT /api/v1/subtasks/{id}
  - DELETE /api/v1/subtasks/{id}
- **Expected Output**: Full CRUD operations for subtasks.

---

## Module 4: CRM

### Task 4.1: Contacts Database & Model
- **Description**: Create Contact model and migration.
- **Related Files**: `database/migrations/*_create_contacts_table.php`, `app/Models/Contact.php`
- **API Endpoints**: N/A
- **Expected Output**: Contacts table with id, name, email, phone, organization_id fields.

### Task 4.2: Contacts CRUD APIs
- **Description**: Create API endpoints for contact CRUD operations.
- **Related Files**: `app/Http/Controllers/ContactController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/contacts
  - GET /api/v1/contacts
  - GET /api/v1/contacts/{id}
  - PUT /api/v1/contacts/{id}
  - DELETE /api/v1/contacts/{id}
- **Expected Output**: Full CRUD operations for contacts.

### Task 4.3: Deals & Pipeline Stages Database
- **Description**: Create Deal and PipelineStage models and migrations.
- **Related Files**: 
  - `database/migrations/*_create_pipeline_stages_table.php`
  - `database/migrations/*_create_deals_table.php`
- **API Endpoints**: N/A
- **Expected Output**: Deals table with id, title, value, stage_id, contact_id, owner_id fields.

### Task 4.4: Deals CRUD APIs
- **Description**: Create API endpoints for deal CRUD operations.
- **Related Files**: `app/Http/Controllers/DealController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/deals
  - GET /api/v1/deals
  - GET /api/v1/deals/{id}
  - PUT /api/v1/deals/{id}
  - DELETE /api/v1/deals/{id}
- **Expected Output**: Full CRUD operations for deals.

### Task 4.5: Pipeline Stages CRUD APIs
- **Description**: Create API endpoints for pipeline stage management.
- **Related Files**: `app/Http/Controllers/DealController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/stages
  - GET /api/v1/stages
- **Expected Output**: CRUD operations for pipeline stages.

---

## Module 5: Library & Subscriptions

### Task 5.1: Library Content Database & Model
- **Description**: Create LibraryContent model and migration.
- **Related Files**: `database/migrations/*_create_library_contents_table.php`, `app/Models/LibraryContent.php`
- **API Endpoints**: N/A
- **Expected Output**: Library contents table with id, title, type, description, access_level fields.

### Task 5.2: Subscription Plans Database & Model
- **Description**: Create SubscriptionPlan model and migration.
- **Related Files**: `database/migrations/*_create_subscription_plans_table.php`, `app/Models/SubscriptionPlan.php`
- **API Endpoints**: N/A
- **Expected Output**: Subscription plans table with id, name, tier, price, features fields.

### Task 5.3: User Subscriptions Database & Model
- **Description**: Create UserSubscription model and migration to track user subscriptions.
- **Related Files**: `database/migrations/*_create_user_subscriptions_table.php`, `app/Models/UserSubscription.php`
- **API Endpoints**: N/A
- **Expected Output**: User subscriptions table linking users to their active plans.

### Task 5.4: Subscription Restriction Middleware
- **Description**: Create middleware to restrict content access based on subscription tier.
- **Related Files**: `app/Http/Middleware/SubscriptionMiddleware.php`
- **API Endpoints**: N/A
- **Expected Output**: Middleware that checks subscription level before granting content access.

### Task 5.5: Library Content CRUD APIs
- **Description**: Create API endpoints for library content CRUD operations.
- **Related Files**: `app/Http/Controllers/LibraryController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/content
  - GET /api/v1/content
  - GET /api/v1/content/{id}
  - PUT /api/v1/content/{id}
  - DELETE /api/v1/content/{id}
- **Expected Output**: Full CRUD operations for library content.

### Task 5.6: Subscriptions CRUD APIs
- **Description**: Create API endpoints for subscription management.
- **Related Files**: `app/Http/Controllers/SubscriptionController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/subscriptions
  - GET /api/v1/subscriptions
  - GET /api/v1/subscriptions/{id}
  - PUT /api/v1/subscriptions/{id}
  - DELETE /api/v1/subscriptions/{id}
- **Expected Output**: Full CRUD operations for subscriptions.

---

## Module 6: LMS

### Task 6.1: Courses Database & Model
- **Description**: Create Course model and migration.
- **Related Files**: `database/migrations/*_create_courses_table.php`, `app/Models/Course.php`
- **API Endpoints**: N/A
- **Expected Output**: Courses table with id, title, description, instructor_id, status fields.

### Task 6.2: Courses CRUD APIs
- **Description**: Create API endpoints for course CRUD operations.
- **Related Files**: `app/Http/Controllers/CourseController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/courses
  - GET /api/v1/courses
  - GET /api/v1/courses/{id}
  - PUT /api/v1/courses/{id}
  - DELETE /api/v1/courses/{id}
- **Expected Output**: Full CRUD operations for courses.

### Task 6.3: Sections Database & Model
- **Description**: Create Section model and migration for course sections.
- **Related Files**: `database/migrations/*_create_sections_table.php`, `app/Models/Section.php`
- **API Endpoints**: N/A
- **Expected Output**: Sections table with id, title, order, course_id fields.

### Task 6.4: Sections CRUD APIs
- **Description**: Create API endpoints for section CRUD operations.
- **Related Files**: `app/Http/Controllers/CourseController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/sections
  - GET /api/v1/sections
  - GET /api/v1/sections/{id}
  - PUT /api/v1/sections/{id}
  - DELETE /api/v1/sections/{id}
- **Expected Output**: Full CRUD operations for course sections.

### Task 6.5: Lessons Database & Model
- **Description**: Create Lesson model and migration for course lessons.
- **Related Files**: `database/migrations/*_create_lessons_table.php`, `app/Models/Lesson.php`
- **API Endpoints**: N/A
- **Expected Output**: Lessons table with id, title, content, type, section_id, order fields.

### Task 6.6: Lessons CRUD APIs
- **Description**: Create API endpoints for lesson CRUD operations.
- **Related Files**: `app/Http/Controllers/CourseController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/lessons
  - GET /api/v1/lessons
  - GET /api/v1/lessons/{id}
  - PUT /api/v1/lessons/{id}
  - DELETE /api/v1/lessons/{id}
- **Expected Output**: Full CRUD operations for lessons.

### Task 6.7: Enrollments Database & Model
- **Description**: Create Enrollment model and migration to track user course enrollments.
- **Related Files**: `database/migrations/*_create_enrollments_table.php`, `app/Models/Enrollment.php`
- **API Endpoints**: N/A
- **Expected Output**: Enrollments table with id, user_id, course_id, progress, status fields.

### Task 6.8: Enrollments CRUD APIs
- **Description**: Create API endpoints for enrollment management.
- **Related Files**: `app/Http/Controllers/CourseController.php`, `routes/api.php`
- **API Endpoints**:
  - POST /api/v1/enrollments
  - GET /api/v1/enrollments
  - GET /api/v1/enrollments/{id}
- **Expected Output**: Users can enroll in courses and view their enrollments.

---

## Module 7: Frontend

### Task 7.1: Main Layout Component
- **Description**: Create reusable layout component with header, sidebar, and footer.
- **Related Files**: `resources/js/layouts/MainLayout.vue`, `resources/js/components/`
- **API Endpoints**: N/A
- **Expected Output**: Consistent layout with navigation, responsive design for mobile.

### Task 7.2: Authentication Pages
- **Description**: Create login and registration pages using Inertia.js and Vue.
- **Related Files**: `resources/js/pages/Auth/Login.vue`, `resources/js/pages/Auth/Register.vue`
- **API Endpoints**: N/A
- **Expected Output**: Functional login and registration forms with validation.

### Task 7.3: Dashboard Page
- **Description**: Create main dashboard page with user stats and quick actions.
- **Related Files**: `resources/js/pages/Dashboard.vue`
- **API Endpoints**: N/A
- **Expected Output**: Dashboard showing user overview and navigation to key features.

### Task 7.4: Projects Pages
- **Description**: Create Inertia pages for project listing, details, and forms.
- **Related Files**: 
  - `resources/js/pages/Projects/Index.vue`
  - `resources/js/pages/Projects/Show.vue`
  - `resources/js/pages/Projects/Create.vue`
  - `resources/js/pages/Projects/Edit.vue`
- **API Endpoints**: N/A
- **Expected Output**: Full project management UI with CRUD operations.

### Task 7.5: Tasks Pages
- **Description**: Create Inertia pages for task management with filters.
- **Related Files**: 
  - `resources/js/pages/Tasks/Index.vue`
  - `resources/js/pages/Tasks/Show.vue`
  - `resources/js/pages/Tasks/Create.vue`
  - `resources/js/pages/Tasks/Edit.vue`
- **API Endpoints**: N/A
- **Expected Output**: Task listing with today/overdue/upcoming filters and subtask support.

### Task 7.6: CRM Pages
- **Description**: Create Inertia pages for contacts and deals management.
- **Related Files**: 
  - `resources/js/pages/Contacts/Index.vue`
  - `resources/js/pages/Contacts/Show.vue`
  - `resources/js/pages/Deals/Index.vue`
  - `resources/js/pages/Deals/Show.vue`
- **API Endpoints**: N/A
- **Expected Output**: Contact listing and deal pipeline view with stage management.

### Task 7.7: Library Pages
- **Description**: Create Inertia pages for library content browsing.
- **Related Files**: 
  - `resources/js/pages/Library/Index.vue`
  - `resources/js/pages/Library/Show.vue`
- **API Endpoints**: N/A
- **Expected Output**: Content browsing with subscription tier filtering.

### Task 7.8: LMS Pages
- **Description**: Create Inertia pages for course browsing and lessons.
- **Related Files**: 
  - `resources/js/pages/Courses/Index.vue`
  - `resources/js/pages/Courses/Show.vue`
  - `resources/js/pages/Lessons/Show.vue`
- **API Endpoints**: N/A
- **Expected Output**: Course listing, enrollment, and lesson viewer.

### Task 7.9: State Management
- **Description**: Implement Pinia stores for centralized state management.
- **Related Files**: `resources/js/stores/user.js`, `resources/js/stores/projects.js`, `resources/js/stores/tasks.js`
- **API Endpoints**: N/A
- **Expected Output**: Pinia stores for user, projects, tasks, and CRM data with optimistic updates.

---

## Requirement to Task Mapping

| Requirement | Task(s) |
|-------------|---------|
| Organization CRUD | 2.3 |
| User CRUD | 2.4 |
| Role CRUD | 2.5 |
| User Role Assignment | 2.6 |
| Projects CRUD | 3.2 |
| Tasks CRUD | 3.4 |
| Task Filters | 3.5 |
| Subtasks CRUD | 3.7 |
| Contacts CRUD | 4.2 |
| Deals CRUD | 4.4 |
| Pipeline Stages | 4.5 |
| Library Content CRUD | 5.5 |
| Subscriptions CRUD | 5.6 |
| Courses CRUD | 6.2 |
| Sections CRUD | 6.4 |
| Lessons CRUD | 6.6 |
| Enrollments | 6.8 |
