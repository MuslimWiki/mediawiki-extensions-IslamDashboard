# Testing the IslamDashboard Extension

This document provides instructions for running tests for the IslamDashboard extension.

## Prerequisites

- PHP 7.4 or later
- Composer
- A working MediaWiki installation
- The IslamDashboard extension installed

## Running Tests

### PHPUnit Tests

1. Navigate to your MediaWiki installation directory
2. Run the following command to execute all tests:
   ```bash
   composer phpunit extensions/IslamDashboard/tests/phpunit/
   ```

3. To run a specific test file:
   ```bash
   composer phpunit extensions/IslamDashboard/tests/phpunit/unit/Widgets/DashboardWidgetTest.php
   ```

### QUnit Tests

1. Start the PHP development server:
   ```bash
   php -S 0.0.0.0:8080 -t /path/to/mediawiki
   ```

2. In another terminal, run:
   ```bash
   npm test -- --config qunit.json
   ```

## Writing Tests

### PHPUnit Tests

- Place unit tests in `tests/phpunit/unit/`
- Place integration tests in `tests/phpunit/integration/`
- Test classes should extend `MediaWikiIntegrationTestCase`
- Use PHPUnit's assertion methods for testing

### QUnit Tests

- Place QUnit tests in `tests/qunit/`
- Test files should be named `test.[testname].js`
- Use QUnit's assertion methods for testing

## Test Coverage

To generate a test coverage report:

```bash
php -d xdebug.mode=coverage vendor/bin/phpunit --coverage-html coverage/ extensions/IslamDashboard/tests/phpunit/
```

## Troubleshooting

If you encounter issues with the test environment:

1. Ensure all dependencies are installed:
   ```bash
   composer install
   npm install
   ```

2. Clear the MediaWiki cache:
   ```bash
   php maintenance/rebuildLocalisationCache.php
   php maintenance/rebuildFileCache.php
   ```

3. Check PHP error logs for specific error messages

## Continuous Integration

This extension includes a `.github/workflows/ci.yml` file for GitHub Actions that runs:
- PHPUnit tests
- PHPCS code style checks
- JavaScript linting

## Contributing

When contributing tests, please ensure:

1. Tests are focused and test one specific piece of functionality
2. Test methods are properly documented
3. All tests pass before submitting a pull request
4. New features include corresponding tests
5. Bug fixes include regression tests
