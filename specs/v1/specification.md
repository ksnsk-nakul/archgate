# FluxHaven v1 Functional Requirements

## Core Modules

### 1. RBAC + Organization Management
- Create and manage organizations.
- Assign users to organizations with different roles (Admin, Manager, Member).
- Manage user permissions based on organization roles.

### 2. Task & Project Management
- Create projects and organize them into boards.
- Add tasks within projects with subtasks, tags, and priorities.
- Track task progress and completion status.
- Assign tasks to team members.
- Set deadlines and reminders for tasks.

### 3. Simple CRM (Contacts + Deals)
- Manage contacts with basic information (name, email, phone).
- Create deals with associated contacts.
- Track deal stages and progression.
- Generate reports on lead tracking.

### 4. Library System (Content + Subscription Restriction)
- Manage digital content (book, article, video).
- Restrict access to content based on user subscriptions.
- Allow users to subscribe to different plans (free, premium).

### 5. Basic LMS (Courses for Developers/Freshers)
- Create courses with sections and lessons.
- Assign courses to learners.
- Track learner progress and completion status.

## Key Features

- Subdomain-based tenancy (e.g., org1.app.com).
- Multi-tenant SaaS architecture.
- Modular system (Projects, CRM, Library, LMS).
- Role-based access and permissions.
- Subscription-based content access.
- CRM pipeline with basic lead tracking.
- User profile & settings.

## Non-Functional Requirements

### 1. Performance
- API endpoints must respond within 500ms at p95 under normal load.
- Optimize database queries to maintain response time targets.

### 2. Scalability
- System must support at least 100 organizations and 10,000 total users at launch.
- Implement caching mechanisms (Redis) for roles, permissions, and frequently read data.

### 3. Security
- Use Sanctum for authentication and authorization.
- Implement HTTPS for secure data transmission.
- Regularly update dependencies to patch security vulnerabilities.

### 4. Usability
- Create intuitive user interfaces for both backend management and frontend application.
- Provide documentation and support resources for users.

## User Roles

### 1. Admin
- Full access to all modules and settings.
- Manage organizations, users, roles, and permissions.

### 2. Manager
- Access to project management, task management, CRM pipeline, and library system.
- Create and manage projects and tasks.
- Track deal pipeline and content access.

### 3. Member
- Access to assigned projects, tasks, and CRM leads.
- View and manage their own profile settings.

## API Endpoints

### Authentication & User Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/register | Register new user |
| POST | /api/v1/login | User login |
| POST | /api/v1/logout | User logout |
| GET | /api/v1/me | Get current authenticated user |
| POST | /api/v1/users | Create user |
| GET | /api/v1/users/{id} | Get user by ID |
| PUT | /api/v1/users/{id} | Update user |
| DELETE | /api/v1/users/{id} | Delete user |

### Organization Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/organizations | Create organization |
| GET | /api/v1/organizations/{id} | Get organization by ID |
| PUT | /api/v1/organizations/{id} | Update organization |
| DELETE | /api/v1/organizations/{id} | Delete organization |

### Roles & Permissions
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/roles | Create role |
| GET | /api/v1/roles/{id} | Get role by ID |
| PUT | /api/v1/roles/{id} | Update role |
| DELETE | /api/v1/roles/{id} | Delete role |
| POST | /api/v1/user-role-assignments | Assign role to user |

### Project Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/projects | Create project |
| GET | /api/v1/projects | List projects |
| GET | /api/v1/projects/{id} | Get project by ID |
| PUT | /api/v1/projects/{id} | Update project |
| DELETE | /api/v1/projects/{id} | Delete project |

### Task Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/tasks | Create task |
| GET | /api/v1/tasks | List tasks |
| GET | /api/v1/tasks/{id} | Get task by ID |
| PUT | /api/v1/tasks/{id} | Update task |
| DELETE | /api/v1/tasks/{id} | Delete task |
| GET | /api/v1/tasks/today | Get today's tasks |
| GET | /api/v1/tasks/overdue | Get overdue tasks |
| GET | /api/v1/tasks/upcoming | Get upcoming tasks |

### Subtask Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/tasks/{id}/subtasks | Create subtask |
| GET | /api/v1/subtasks | List subtasks |
| GET | /api/v1/subtasks/{id} | Get subtask by ID |
| PUT | /api/v1/subtasks/{id} | Update subtask |
| DELETE | /api/v1/subtasks/{id} | Delete subtask |

### CRM - Contacts
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/contacts | Create contact |
| GET | /api/v1/contacts | List contacts |
| GET | /api/v1/contacts/{id} | Get contact by ID |
| PUT | /api/v1/contacts/{id} | Update contact |
| DELETE | /api/v1/contacts/{id} | Delete contact |

### CRM - Deals & Pipeline
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/deals | Create deal |
| GET | /api/v1/deals | List deals |
| GET | /api/v1/deals/{id} | Get deal by ID |
| PUT | /api/v1/deals/{id} | Update deal |
| DELETE | /api/v1/deals/{id} | Delete deal |
| POST | /api/v1/stages | Create pipeline stage |
| GET | /api/v1/stages | List pipeline stages |

### CRM - Reports
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/v1/reports/leads | Lead tracking report (deals by stage, owner, date range) |

### Library - Content
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/content | Create content |
| GET | /api/v1/content | List content |
| GET | /api/v1/content/{id} | Get content by ID |
| PUT | /api/v1/content/{id} | Update content |
| DELETE | /api/v1/content/{id} | Delete content |

### Library - Subscriptions
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/subscriptions | Create subscription |
| GET | /api/v1/subscriptions | List subscriptions |
| GET | /api/v1/subscriptions/{id} | Get subscription by ID |
| PUT | /api/v1/subscriptions/{id} | Update subscription |
| DELETE | /api/v1/subscriptions/{id} | Delete subscription |

### LMS - Courses
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/courses | Create course |
| GET | /api/v1/courses | List courses |
| GET | /api/v1/courses/{id} | Get course by ID |
| PUT | /api/v1/courses/{id} | Update course |
| DELETE | /api/v1/courses/{id} | Delete course |

### LMS - Sections
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/sections | Create section |
| GET | /api/v1/sections | List sections |
| GET | /api/v1/sections/{id} | Get section by ID |
| PUT | /api/v1/sections/{id} | Update section |
| DELETE | /api/v1/sections/{id} | Delete section |

### LMS - Lessons
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/lessons | Create lesson |
| GET | /api/v1/lessons | List lessons |
| GET | /api/v1/lessons/{id} | Get lesson by ID |
| PUT | /api/v1/lessons/{id} | Update lesson |
| DELETE | /api/v1/lessons/{id} | Delete lesson |

### LMS - Enrollments
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/enrollments | Enroll user in course |
| GET | /api/v1/enrollments | List enrollments |
| GET | /api/v1/enrollments/{id} | Get enrollment by ID |
| PUT | /api/v1/enrollments/{id} | Update enrollment progress |

## Summary

FluxHaven v1 is designed to be a modular, multi-tenant SaaS platform with features for RBAC, organization management, task/project management, CRM, library system, and LMS. The platform will support subdomain-based tenancy, role-based access control, and ensure high performance and security.
