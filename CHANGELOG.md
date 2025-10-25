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
