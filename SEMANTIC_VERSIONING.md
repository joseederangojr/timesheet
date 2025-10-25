# Semantic Versioning & Release Process

This project uses automated semantic versioning and releases powered by [semantic-release](https://semantic-release.gitbook.io/).

## How It Works

### Conventional Commits

All commits must follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

### Commit Types

- `feat`: A new feature (triggers minor release)
- `fix`: A bug fix (triggers patch release)
- `docs`: Documentation only changes (triggers patch release)
- `style`: Changes that do not affect the meaning of the code (triggers patch release)
- `refactor`: A code change that neither fixes a bug nor adds a feature (triggers patch release)
- `perf`: A code change that improves performance (triggers patch release)
- `test`: Adding missing tests or correcting existing tests
- `build`: Changes that affect the build system or external dependencies
- `ci`: Changes to our CI configuration files and scripts
- `chore`: Other changes that don't modify src or test files
- `revert`: Reverts a previous commit

### Breaking Changes

Use `BREAKING CHANGE:` in the footer or add `!` after the type to trigger a major release:

```
feat!: drop support for PHP 8.3

BREAKING CHANGE: minimum PHP version is now 8.4
```

## Release Process

### Automatic Releases

1. **Push to main branch** - triggers the release workflow
2. **Tests run** - all tests must pass
3. **Semantic analysis** - analyzes commits since last release
4. **Version calculation** - determines next version (major.minor.patch)
5. **Changelog generation** - updates CHANGELOG.md
6. **Version update** - updates composer.json via `php artisan version set`
7. **Git commit** - commits the changes with `[skip ci]`
8. **GitHub release** - creates release with tag and notes

### Manual Commits

Use the interactive commit tool:

```bash
npm run commit
```

This will prompt you through creating a proper conventional commit.

### Version Bumping Rules

- **Patch (0.0.1)**: `fix`, `docs`, `style`, `refactor`, `perf`
- **Minor (0.1.0)**: `feat`
- **Major (1.0.0)**: Any commit with `BREAKING CHANGE` or `!`

## Development Workflow

1. **Create feature branch**: `git checkout -b feature/new-feature`
2. **Make changes and commit**: Use `npm run commit` for guided commits
3. **Push and create PR**: All commits are validated via commitlint
4. **Merge to main**: Triggers automatic release process

## Configuration Files

- `.releaserc.json` - Semantic release configuration
- `commitlint.config.js` - Commit message linting rules
- `.github/workflows/release.yml` - GitHub Actions release workflow
- `.husky/commit-msg` - Git hook for commit validation

## Commands

```bash
# Interactive commit (recommended)
npm run commit

# Manual release (for testing)
npm run release

# Show current version
php artisan version show

# Manual version bump
php artisan version bump [major|minor|patch]
php artisan version set --ver=1.2.3
```

## Example Commits

```bash
# Feature (minor release)
feat: add user authentication system

# Bug fix (patch release)
fix: resolve login redirect issue

# Breaking change (major release)
feat!: migrate to new authentication system

BREAKING CHANGE: old authentication tokens are no longer valid
```
