## 1.0.0 (2025-10-25)

### Features

* add authentication controllers for magic link and password login ([145c0f7](https://github.com/tailoredstack/timesheet/commit/145c0f7c0cbc007e8bd48f2df959fa21fb89380f))
* add authentication form request validation classes ([3a0a797](https://github.com/tailoredstack/timesheet/commit/3a0a797084141971ed9f50d3048248e6670c99d5))
* add authentication routes for login, logout, and magic links ([f7cf28d](https://github.com/tailoredstack/timesheet/commit/f7cf28d654b88f8b82b52603bafebb3b91735ca7))
* add dashboard page with role-based welcome messages ([b8c7482](https://github.com/tailoredstack/timesheet/commit/b8c748221fe8a06cb2f4f94e41582592d0f54fcb))
* add database migrations for sessions and password resets ([ca6a385](https://github.com/tailoredstack/timesheet/commit/ca6a3855816ec754070ffd1fbbcf548f40e7d63a))
* add magic link email notification ([23aa385](https://github.com/tailoredstack/timesheet/commit/23aa3852f030a5fe9430fd595afec15a1b487367))
* add semantic versioning and automated release system ([2dd6634](https://github.com/tailoredstack/timesheet/commit/2dd66348d2549384212544361ca56bcdbff54193))
* configure project for Timesheet SaaS application ([396edd0](https://github.com/tailoredstack/timesheet/commit/396edd0955b4e87e6d8030119cfeb50bf83480b5))
* create dual authentication login page with magic link and password options ([caff124](https://github.com/tailoredstack/timesheet/commit/caff12468c00adbc83508878723787758d3269ac))
* implement user role system with RBAC ([4ef7f17](https://github.com/tailoredstack/timesheet/commit/4ef7f17fb293fd3a0f14325de8fd8ae4d09a3631))
* improve VersionCommand error handling and test coverage ([7923dd8](https://github.com/tailoredstack/timesheet/commit/7923dd88a808b514637aac9f8b4b6a151dc520a1))

### Bug Fixes

* improve MagicLinkController error handling and test coverage ([72a3ac5](https://github.com/tailoredstack/timesheet/commit/72a3ac544fdd60584a2ade1c61bba024ef416645))
* simplify PasswordController authentication flow ([6692342](https://github.com/tailoredstack/timesheet/commit/6692342df0cf26a79376a52d6d364a22d49d1a8f))

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2025-10-25

### Added

- Initial timesheet application setup with Laravel 12 and Inertia.js React
- User authentication and role-based access control system
- User and Role models with many-to-many relationships
- Database migrations for users, roles, and user_roles tables
- Role-based query classes (CheckUserHasRoleQuery, CheckUserIsAdminQuery, etc.)
- Comprehensive test suite for User and Role models
- RoleFactory and UserFactory for testing
- RoleSeeder for initial role data
- Semantic versioning system with `php artisan version` command
- shadcn/ui component library integration
- Tailwind CSS v4 styling system
- Laravel Pint code formatting
- Pest testing framework with browser testing support
- PHPStan static analysis
- Pre-commit hooks with Husky

### Enhanced

- UserTest with relationship testing (attach/detach/sync roles)
- Password hashing verification tests
- RoleTest covering instantiation, traits, fillable attributes, relationships, timestamps, and many-to-many relationship integrity
- RoleFactory to properly generate fake data for name and description fields
- Tests for many-to-many relationship operations and data integrity
