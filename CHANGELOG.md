# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Comprehensive model tests for User and Role models
- Enhanced UserTest with relationship testing (attach/detach/sync roles)
- Password hashing verification tests
- Complete RoleTest covering instantiation, traits, fillable attributes, relationships, timestamps, and many-to-many relationship integrity
- Fixed RoleFactory to properly generate fake data for name and description fields
- Tests for many-to-many relationship operations and data integrity
