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
- **REQ-CRM-04**: Public landing page form — visitors submit name, email, phone, interest (learn / buy) to create a Contact + Deal automatically, no login required.
- **REQ-CRM-05**: Simple CMS for landing page — admin edits hero text, tagline, CTA button label, and feature highlights via the admin panel; changes reflect on the public site without a deploy.

### 4. Library System (Content + Subscription Restriction)
- Manage digital content (book, article, video).
- Restrict access to content based on user subscriptions.
- Allow users to subscribe to different plans (free, premium).
- **REQ-LIB-04**: Admin can upload ePub files as library items; the system stores the file and exposes it for in-browser reading.
- **REQ-LIB-05**: In-browser ePub reader — authenticated, subscribed users can read ePub books using a paginated reader (ePub.js or equivalent) without downloading the file.
- **REQ-LIB-06**: FAQ section — admin manages frequently asked questions (question, answer, category, sort order); public FAQ page renders them grouped by category.

### 5. Basic LMS (Courses for Developers/Freshers)
- Create courses with sections and lessons.
- Assign courses to learners.
- Track learner progress and completion status.
- **REQ-LMS-05**: Admin can create a course from a roadmap — paste a structured roadmap (JSON or structured text); the system parses it and scaffolds sections + lessons automatically.
- **REQ-LMS-06**: Roadmap tasks — each lesson can have one or more tasks (description, acceptance criteria, due date). Tasks are assigned to a learner on enrollment.
- **REQ-LMS-07**: Task verification workflow — learner submits a task (with optional attachment/link); tutor reviews and marks verified or rejected with feedback; admin (or permitted subadmin) can override.
- **REQ-LMS-08**: Tutor assignment — admin assigns a tutor to a course or to an individual learner enrollment; tutor sees a dashboard of pending task submissions for review.

## Key Features

- Subdomain-based tenancy (e.g., org1.app.com).
- Multi-tenant SaaS architecture.
- Modular system (Projects, CRM, Library, LMS).
- Role-based access and permissions.
- Subscription-based content access.
- CRM pipeline with basic lead tracking.
- User profile & settings.
- Public landing page with lead-capture form (no auth required).
- Simple admin CMS for landing page content.
- ePub reader for library books.
- FAQ section (admin-managed, public-facing).
- Roadmap-to-course scaffolding for LMS.
- Task verification workflow (learner → tutor → admin).
- Tutor dashboard for pending task reviews.

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

---

## New Features Added (2026-04-30 → 2026-05-01)

### 6. Landing Site CMS (Multi-page Public Site)
- **REQ-LAND-01**: Public landing site with multiple pages: Home, Services, Library Preview, About, Careers, Contact Us — accessible without login.
- **REQ-LAND-02a**: CMS opens to a **page list** (Home, Services, About, Careers, Contact, Library Preview, Footer/Nav). Each page row shows its slug, an enabled/disabled toggle (Home always enabled), and an Edit link. Page list order matches site navigation.
- **REQ-LAND-02b**: Each page has its own dedicated editor with one or more **section cards** relevant to that page. Sections per page — Home: Hero + Services teaser + About teaser + Careers teaser + Contact CTA; Services: Services grid; About: Rich text body; Careers: Job listings; Contact: Contact info fields; Library Preview: preview card list; Footer/Nav: nav links + footer text.
- **REQ-LAND-02c**: Each section card has a title, content fields (rich text or structured), enabled toggle, and display order. Section cards optionally carry a `link_to` field (internal route or anchor) that renders a "View more →" link on the public page — e.g. a Careers teaser card on Home links to `/careers`.
- **REQ-LAND-02d**: Every page editor toolbar has a **Preview toggle** button. Activating it splits the layout into editor (left) + live preview (right), updating in real time as the admin types. Preview is off by default on structured-field-only tabs (Nav, Contact, Footer) and on by default for rich-content tabs.
- **REQ-LAND-03**: All public pages share a `PublicLayout.vue` component. The top nav and footer are rendered entirely from CMS `nav_links` and `footer_text`; no hardcoded copy.
- **REQ-LAND-04**: Library preview page (`/library-preview`) lists books/articles visible to all visitors; clicking "Read" or "Buy" requires sign-in and redirects to the auth flow.
- **REQ-LAND-05**: Lead capture form on Home and Contact pages — submits to CRM as Contact + Deal (stage: new), no login required, honeypot-protected, rate-limited 5 req/min per IP.
- **REQ-LAND-06**: Settings sidebar in the app includes Admin section (App Settings, Third-party, Landing CMS) visible only to admin users.
- **REQ-LAND-07**: AppSidebar has a CRM group (Contacts, Deals) and an Admin group (App settings, Landing CMS, Roles) visible only to admin users.
- **REQ-LAND-08**: All CMS data is cached with a 5-minute TTL (`settings:landing`). Any CMS save immediately calls `Cache::forget('settings:landing')` so the public site reflects changes without a deploy.
- **REQ-LAND-09**: `RichEditor.vue` — native `contenteditable` rich text component (Bold, Italic, Underline, Bullet list, Ordered list, H2, H3, Paragraph, Remove formatting) used for hero subtitle, about body, and career description fields in the CMS.
