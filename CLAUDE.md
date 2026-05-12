# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## DigiProper

Property management platform for Indian property owners. Currently a Laravel 13 web app (Blade + Tailwind + Alpine). A mobile app and JSON API are planned but not yet built.

## What exists vs. what doesn't

Be honest about reality when reasoning about the codebase:

- **Built**: Web UI (Blade), Google OAuth sign-in (Socialite), Property CRUD with documents, Dashboard summary, global ⌘K command palette, multi-tenant isolation via global owner scope.
- **Not built**: `/mobile/` directory, `routes/api.php`, Sanctum tokens, any `/api/v1/*` endpoint, policies, jobs, queues in use, service/action layer. The only JSON endpoint is `GET /search` (powers the command palette).

When asked to add something API- or mobile-shaped, surface that nothing exists yet and confirm the approach before scaffolding.

## Commands

- `composer dev` — concurrent dev: `artisan serve` + `queue:listen` + `pail` + `vite`. Default entry point.
- `composer setup` — fresh install (install, key:generate, migrate, npm install, npm run build).
- `./vendor/bin/pest` (or `composer test`) — run the full Pest suite.
- `php artisan test --compact --filter=PropertyCrudTest` — run a single test.
- `vendor/bin/pint --dirty --format agent` — format dirty PHP files before declaring backend work done.
- `npm run dev` / `npm run build` — Vite dev server / production bundle.

## Architectural invariants

These are easy to break without noticing. Do not violate without an explicit reason.

- **Global owner scope on `Property`** — every `Property::query()` (including `all()`, `find()`, eager loads) is auto-filtered to the current `auth()->id()`'s rows via a global scope set on the model. Don't add per-controller `where('created_by', …)` on top; don't reach for `withoutGlobalScopes()` casually. New per-user models should follow the same pattern, not re-implement filtering in controllers.
- **`HasAudit` trait** (`app/Models/Concerns/HasAudit.php`) — auto-populates `created_by` / `updated_by` / `deleted_by` from `auth()->id()` on model events. `deleted_by` is written via a second update inside the `deleting` event (Eloquent doesn't fire on the soft-delete touch otherwise). Apply this trait to any new audited model and add the matching columns via `$table->auditUsers()` in the migration (already a custom Blueprint macro used by the existing migrations).
- **Soft deletes** on `Property` and `PropertyDocument`. Hard-delete only with intent.
- **No service / action / job layer.** Controllers do CRUD directly; that's the current convention. Don't introduce abstraction layers without approval.
- **OAuth tokens are encrypted at rest** (`OauthAccount`'s `access_token` / `refresh_token` use the `encrypted` cast and are in `$hidden`). Never log them, never serialize the model into a log line.
- **Prefer normalized child tables over JSON columns** for new structured/PII data. `Property.contacts` is a legacy JSON column; new collections should be modelled as separate tables with per-field encryption where PII is involved.

## Auth

- **Web**: Laravel Breeze sessions. Auth routes in `routes/auth.php`.
- **Google OAuth**: Socialite, wired through `app/Http/Controllers/Auth/GoogleAuthController.php`. Routes: `GET /auth/google/redirect`, `GET /auth/google/callback`. Linkage stored in `oauth_accounts` table (one row per provider per user).
- **Local dev OAuth quirk**: Google rejects `.test` TLDs in OAuth redirect URIs. Local dev must run on `http://localhost:8000` for the OAuth flow to work end-to-end.
- **No Sanctum, no token guard, no API auth.** When the API is built, decide between Sanctum (personal access tokens) and Passport explicitly.

## Domain model

- `User` ←hasMany→ `OauthAccount` — Google linkage; tokens encrypted, hidden.
- `User` ←hasMany→ `Property` — enforced by the global owner scope, not a FK relation traversal.
- `Property` ←hasMany→ `PropertyDocument` — FK with `cascadeOnDelete`; both sides soft-deleted.
- `Property` carries: address (line1/line2/city/state/pincode), tenure & tenant info, area + unit, financials (`imputed_value_inr`, `rent_yearly_inr`, `yield_percent` — all decimal), JSON `contacts`, `keys_location`, optional RERA number, `is_data_complete` flag. Source of truth: `database/migrations/2026_05_12_175432_create_properties_table.php`.
- `Property::effectiveYieldPercent` — computed accessor; returns `yield_percent` if set, otherwise derives from `rent_yearly_inr / imputed_value_inr`. Use it for display; don't recompute in views.
- `app/Support/IndianStates.php` — canonical list of state codes. Reuse for state validation and dropdowns.

## Validation

Validation lives in form requests, not in controllers. Always extend the existing pattern:

- `app/Http/Requests/StorePropertyRequest.php`
- `app/Http/Requests/UpdatePropertyRequest.php`
- `app/Http/Requests/StorePropertyDocumentRequest.php`

Authorization currently just checks `$this->user() !== null`; tightening is a deliberate future change (introduce policies then), not something to add ad-hoc.

## UI system

Blade + Tailwind + Alpine. The design system is intentional — reuse tokens and components instead of inventing one-off styles.

**Color tokens** (`tailwind.config.js`):
- `primary-*` — brand purple. CTAs, focus rings, nav highlights.
- `accent-*` — warm peach. Secondary highlights, stat-card tints.
- `ink-*` — neutral text (dark-mode aware). Use these instead of `gray-*`.
- `surface-*` — ultra-light backgrounds.

**Hero gradients**: `bg-hero-purple` (default), `bg-hero-warm`, `bg-hero-night`. Heroes stay **single-hue purple** by default — no purple-to-peach bleeds on big hero surfaces.

**Shadow scale**: `shadow-soft` (cards) → `shadow-soft-lg` (elevated) → `shadow-glow` / `shadow-glow-sm` (purple-glow CTAs).

**Radius ladder**: `rounded-xl` (inputs/buttons) → `rounded-2xl` (cards) → `rounded-3xl` (heroes, section-cards).

**Font**: Figtree (loaded from bunny.net in layouts).

**Reuse, don't invent.** Component library lives in `resources/views/components/`:

- `<x-section-card title="…" icon="…">` — grouped content block.
- `<x-stat-card label="…" value="…" tint="primary|accent|emerald|sky|neutral">` — KPI tile.
- `<x-page-hero tone="purple|warm|night">` — page header.
- `<x-primary-button>` / `<x-secondary-button>` / `<x-danger-button>` / `<x-icon-button tone="ghost|primary|danger">`.
- `<x-text-input>` / `<x-textarea>` / `<x-select>` / `<x-input-label>` / `<x-input-error>` / `<x-form-section>`.
- `<x-status-badge>`, `<x-chip>`, `<x-empty-state>`, `<x-skeleton>`.
- `<x-icon name="…">` — inline SVG (Heroicons v2). Add new glyphs to `components/icon.blade.php` rather than pulling a runtime.
- `<x-inr :amount="…">` — INR currency formatting with Indian digit grouping.

## Layout & navigation

- **Authenticated layout**: `resources/views/layouts/app.blade.php` mounts the sidebar, topbar, mobile brand header, bottom nav, and the global command palette in one place.
- **Desktop**: `components/sidebar.blade.php` (sticky left) + `components/topbar.blade.php` (sticky top, hosts the ⌘K trigger and notifications).
- **Mobile**: mobile brand header on top + `components/bottom-nav.blade.php` fixed at bottom. Page content reserves `pb-24` for bottom-nav clearance.

**Command palette / search pattern**:
- Global `<x-command-palette>` is mounted once in `layouts/app.blade.php`. Opens on ⌘K / Ctrl+K.
- Backed by `SearchController@index` (route `GET /search`, name `search`), returning JSON `{ properties: [...] }`. This is currently the only JSON endpoint in the app.
- **To make a new entity searchable**: add a query branch in `SearchController`, then add a results section in `command-palette.blade.php`'s Alpine `commandPalette()` state machine.

## India-specific (always apply)

- **Currency**: INR. Format with `₹` and Indian digit grouping (`1,00,000`, not `100,000`). Use `<x-inr>` in Blade.
- **Phone**: `+91`, 10-digit. Validate accordingly.
- **Timezone**: target is `Asia/Kolkata` (IST). Currently `config/app.php` still says `UTC` — flagged below.
- **Address**: line1, line2, city, state (28 states + 8 UTs via `IndianStates`), 6-digit PIN code.
- **PII** (PAN, Aadhaar): never log, mask in UI, encrypt at rest, redact in API responses unless explicitly requested. DPDP Act 2023 — minimum data, explicit consent for non-essential fields.
- **GST**: 15-char GSTIN, optional per owner.
- **RERA**: registration number is a property-level optional field.

## Testing

- Pest 4. Bootstrap config in `tests/Pest.php`; Feature tests use `RefreshDatabase` automatically.
- Test DB is **SQLite in-memory** (`phpunit.xml` sets `DB_DATABASE=:memory:`), regardless of the dev DB driver.
- Run a focused test: `php artisan test --compact --filter=PropertyCrudTest`.
- When you change anything user-scoped, write or update an owner-scope test — `tests/Feature/Properties/PropertyAuditTest.php` and the cross-user access tests are the model. Cross-user data access is a regression class that has bitten before.
- Use existing factories (`Property::factory()`, `User::factory()`) — don't hand-build models in tests.

## Workflow conventions

- Run `vendor/bin/pint --dirty --format agent` before declaring backend work done.
- Migrations: descriptive names, never edit a shipped migration — write a new one. Reuse `$table->auditUsers()` for audited tables.
- Route file split: web → `routes/web.php`, auth → `routes/auth.php`. There is no `routes/api.php` yet.
- Prefer named routes and the `route()` helper when generating links.
- Use Boost MCP tools (`database-query`, `database-schema`, `search-docs`) for introspection — they're project-aware.

## Known issues to fix when convenient

- `config/app.php` `'timezone'` is still `'UTC'` — should be `'Asia/Kolkata'`.
- `README.md` has unresolved merge-conflict markers (`=======`, `>>>>>>>`).
- A SQLite file sits at the repo root literally named `DigiProper`. The intended dev DB path is `database/database.sqlite`.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v3

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
