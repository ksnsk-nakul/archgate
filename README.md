# FluxHaven — v1

A modular, multi-tenant SaaS platform built with Laravel 13, Inertia.js v3, Vue 3, and Tailwind CSS v4. FluxHaven combines project management, CRM, a content library, and an LMS into a single, role-based workspace.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.4 · Laravel 13 · Laravel Sanctum v4 · Laravel Fortify v1 |
| Frontend | Vue 3 · Inertia.js v3 · Tailwind CSS v4 · TypeScript |
| Routing (typed) | Laravel Wayfinder (auto-generated TS route functions) |
| Testing | Pest v4 · PHPUnit v12 |
| Code quality | Laravel Pint · ESLint v9 · Prettier v3 |
| Dev environment | Laravel Herd · Laravel Sail |

---

## Modules

### 1. Authentication & RBAC
- Sanctum-based SPA authentication (login, register, logout, `/me`)
- Role-based access control: Admin, Manager, Member
- Permissions system with role → permission pivot tables
- Redis-cached role/permission resolution
- Admin-only UI: Roles list, Permissions editor with visual toggle checkboxes

### 2. Projects & Tasks
- Project CRUD with list/board (Kanban) toggle view
- Tasks with subtasks, priorities, tags, deadlines
- Task filters: Today, Overdue, Upcoming
- Asana-style task panel with inline detail editing
- Deadline reminder notifications (queued, daily schedule)

### 3. CRM — Contacts & Deals
- Contact management (name, email, phone) with create/edit/delete flows
- Deal pipeline with stages (Kanban-style progression)
- Lead tracking report: deals by stage, owner, date range
- **Public lead capture form** — `/contact` and `/` home page
  - Honeypot bot protection + `throttle:5,1` rate limiting
  - Auto-creates `Contact` + `Deal` (stage: new) — no login required

### 4. Content Library
- Library items: book, article, video — with cover image and type badges
- Subscription plans (free / premium) control content access
- Public library preview page (`/library-preview`) — no auth required
- "Read" CTA redirects guests to login with `?intended=` param

### 5. LMS — Courses
- Courses → Sections → Lessons hierarchy
- Enrollment tracking with progress (0–100%) and status
- Course detail page with section/lesson accordion
- Lesson viewer with content rendering

### 6. Public Landing Site (CMS-driven)
- Multi-page public site: Home, Services, About, Careers, Contact, Library Preview
- All pages use `PublicLayout.vue` with CMS-driven nav and footer
- No authentication required for any public page
- Routes: `/`, `/services`, `/about`, `/careers`, `/contact`, `/library-preview`

### 7. Landing CMS (Admin)
- Page-list view: 7 pages each with enabled/disabled toggle
- Per-page section editor: Hero, Services grid, About (rich text), Careers listings, Contact info, Library preview, Footer & Nav
- Live split-panel preview (editor left, preview right) for content pages
- Toggle changes show a confirmation dialog and persist to DB immediately
- 5-minute Redis cache with instant invalidation on save (`Cache::forget('settings:landing')`)
- `RichEditor.vue` — native `contenteditable` component (Bold, Italic, Underline, Lists, H2/H3, Clear formatting)

### 8. App Settings
- App name, logo, favicon upload
- **Brand colour picker** — hex color input + visual swatch
- Primary colour injected as `--primary` CSS variable into `:root` at page load
- All accent colours (buttons, borders, active states) use `var(--primary)` — theme is applied site-wide
- Third-party integrations: SMTP mail, Twilio SMS, Stripe, Razorpay

---

## Application Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # SettingController, RoleManagementController
│   │   ├── Public/         # PublicController (landing pages + lead form)
│   │   ├── Frontend/       # ModulePageController (Inertia page rendering)
│   │   └── ...             # API controllers (ContactController, DealController, etc.)
│   ├── Middleware/
│   │   └── HandleInertiaRequests.php  # Shares appDetails, landing, auth globally
│   └── Requests/Admin/
├── Models/                 # Eloquent models with factories
├── Services/
│   └── SettingService.php  # Reads/writes all settings from DB with caching
└── Policies/

resources/js/
├── components/
│   ├── AppSidebar.vue      # Platform / CRM / Admin nav groups
│   ├── RichEditor.vue      # Native contenteditable rich text
│   └── ui/sidebar/         # Shadcn-style sidebar primitives
├── layouts/
│   ├── AppLayout.vue       # Authenticated app shell
│   ├── AuthLayout.vue      # Login/register shell
│   └── PublicLayout.vue    # Public site nav + footer (CMS-driven)
├── pages/
│   ├── Admin/Settings/     # App, ThirdParty, Landing CMS pages
│   ├── Admin/Roles/        # Index, Permissions
│   ├── Public/             # Services, About, Careers, Contact, LibraryPreview
│   ├── Projects/           # List + Kanban board
│   ├── Tasks/              # Asana-style task list with detail panel
│   ├── Contacts/ Deals/    # CRM pages
│   ├── Library/ Courses/ Lessons/
│   └── Welcome.vue         # Public home page
└── routes/                 # Wayfinder auto-generated typed route functions

database/migrations/        # Sequential timestamps ensure correct FK order:
  000001 organizations
  000002-004 RBAC (roles, permissions, pivots)
  000010-012 CRM (pipeline_stages, contacts, deals)
  000159-161 Library (subscription_plans, library_contents, user_subscriptions)
  000336-339 LMS (courses, sections, lessons, enrollments)
```

---

## Theme System

The primary brand colour is stored in the database (`settings` table, group `app`, key `primary_color`) and defaults to `#f7600d`.

On every page load it is injected as a CSS variable in `app.blade.php`:

```css
:root {
  --primary: #f7600d;
  --primary-hover: color-mix(in srgb, var(--primary) 85%, black);
  --primary-dim:   color-mix(in srgb, var(--primary) 10%, transparent);
  --primary-border: color-mix(in srgb, var(--primary) 20%, transparent);
}
```

All Vue components reference `var(--primary)` via Tailwind arbitrary values (`bg-[var(--primary)]`). Changing the colour in Admin → App Settings applies it across the entire application instantly.

---

## Getting Started

### Prerequisites
- PHP 8.4
- Node.js 20+
- MySQL 8+
- Redis
- [Laravel Herd](https://herd.laravel.com/) (recommended) or Laravel Sail

### Installation

```bash
git clone <repo-url> archgate
cd archgate

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure your `.env`:
```env
DB_DATABASE=archgate
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
SESSION_DRIVER=redis

SANCTUM_STATEFUL_DOMAINS=archgate.test
SESSION_DOMAIN=.archgate.test
```

```bash
php artisan migrate
php artisan db:seed        # Seeds roles, permissions, and a super admin user

npm run build
```

### Development

```bash
composer run dev           # Starts Laravel + Vite + queue worker together
```

Or with Herd — the site is served automatically at `https://archgate.test`.

### Testing

```bash
php artisan test --compact
```

### Code Quality

```bash
vendor/bin/pint             # PHP formatting
npm run lint                # ESLint
npx tsc --noEmit            # TypeScript check
```

---

## Branch Strategy

| Branch | Purpose |
|---|---|
| `main` | Production-ready, stable |
| `module/mvp` | MVP feature collection branch — combines all fix/ and feat/ branches |
| `fix/*` | Bug / migration fixes |
| `feat/*` | New feature branches |

PRs from `fix/` and `feat/` are merged into `module/mvp`, then `module/mvp` is merged to `main` when stable.

---

## Seeded Credentials

After running `php artisan db:seed`, a super admin user is available:

| Field | Value |
|---|---|
| Email | `superadmin@example.com` |
| Password | `password` |
| Role | Super Admin (all permissions) |

---

## Specification

Full functional requirements are in [`specs/v1/specification.md`](specs/v1/specification.md).  
Task breakdown: [`specs/v1/tasks.md`](specs/v1/tasks.md).
