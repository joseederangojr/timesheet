# Code Formatting Guide

This project uses multiple formatters for different file types to ensure consistent code style.

## PHP Formatting

**Primary Tool: Laravel Pint**

- Laravel Pint is the primary PHP formatter following Laravel conventions
- Uses the configuration in `pint.json`
- Run with: `vendor/bin/pint` or `npm run lint:php`
- Check formatting: `vendor/bin/pint --test` or `npm run test:lint:php`

**Secondary Tool: Prettier (PHP Plugin)**

- Prettier with `@prettier/plugin-php` is available for IDE integration
- **Warning**: Running Prettier on PHP files may create conflicts with Pint's formatting
- Use only when you specifically need Prettier's additional formatting features
- Run with: `npm run prettier:php` (use with caution)

**Recommended Workflow:**

1. Use Pint as your primary PHP formatter: `npm run lint:php`
2. Only use Prettier PHP formatting if you need specific features Pint doesn't provide
3. Always run Pint after Prettier to ensure Laravel standards compliance

## JavaScript/TypeScript Formatting

**Primary Tool: Prettier + ESLint**

- Prettier handles code formatting for JS/TS/React files
- ESLint handles linting and code quality
- Run with: `npm run lint:js`
- Check formatting: `npm run test:lint:js`

## Available Commands

### Formatting Commands

- `npm run lint` - Format JS and PHP files (Pint only for PHP)
- `npm run lint:php` - Format PHP files with Pint
- `npm run lint:js` - Format JS/TS files with Prettier + ESLint
- `npm run prettier:php` - Format PHP files with Prettier (use with caution)

### Check Commands

- `npm run test:lint` - Check all file formatting
- `npm run test:lint:php` - Check PHP file formatting with Pint
- `npm run test:lint:js` - Check JS/TS file formatting

## File Coverage

### PHP Files (using Pint)

- `app/**/*.php`
- `database/**/*.php`
- `tests/**/*.php`
- `config/**/*.php`

### JavaScript/TypeScript Files (using Prettier)

- `resources/**/*`

### Excluded from Formatting

- `storage/**`
- `bootstrap/cache/**`
- `vendor/**`
- `*.blade.php` files
- `_ide_helper*.php` files
