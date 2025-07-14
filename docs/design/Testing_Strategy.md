# Testing Strategy

## Version: 0.3.1

## Table of Contents
1. [Test Types](#test-types)
2. [Test Coverage Requirements](#test-coverage-requirements)
3. [Testing Tools](#testing-tools)
4. [Test Environment](#test-environment)
5. [Test Data Management](#test-data-management)
6. [Continuous Integration](#continuous-integration)

## Test Types

### Unit Tests
- **Scope**: Individual PHP classes and JavaScript modules
- **Framework**: PHPUnit (PHP), QUnit (JavaScript)
- **Coverage**: Minimum 80% code coverage
- **Execution**: `composer test:unit`

### Integration Tests
- **Scope**: Component interactions and API endpoints
- **Framework**: MediaWiki's PHPUnit integration
- **Coverage**: Critical paths only
- **Execution**: `composer test:integration`

### Browser Tests
- **Scope**: End-to-end user flows
- **Framework**: Selenium WebDriver
- **Coverage**: Key user journeys
- **Execution**: `npm run test:browser`

## Test Coverage Requirements

### PHP Code
```yaml
# .phpunit.cov.dist
coverage:
  include:
    - includes/
  exclude:
    - includes/autoload.php
  report:
    clover: build/logs/clover.xml
    html: build/coverage
  minimum:
    lines: 80
    methods: 80
    classes: 90
```

### JavaScript Code
```javascript
// .nycrc.json
{
  "extends": "@istanbuljs/nyc-config-typescript",
  "all": true,
  "check-coverage": true,
  "lines": 75,
  "statements": 75,
  "functions": 80,
  "branches": 65
}
```

## Testing Tools

### PHP Testing Stack
- PHPUnit 9.5+
- PHP_CodeSniffer
- PHPStan (static analysis)

### JavaScript Testing Stack
- QUnit 2.19+
- Jest (for React components if used)
- ESLint

## Test Environment

### Requirements
- PHP 7.4-8.1
- Node.js 16+
- Chrome/Chromium for browser tests
- MySQL/MariaDB/PostgreSQL

### Setup
```bash
# Install dependencies
composer install
npm install

# Set up test database
tests/setup-db.sh

# Run all tests
composer test
```

## Test Data Management

### Fixtures
- Store in `tests/phpunit/fixtures/`
- Use JSON for data structures
- Include both success and error cases

### Factories
- Use MediaWiki's TestUser class for user creation
- Implement data builders for complex objects

## Continuous Integration

### GitHub Actions
```yaml
# .github/workflows/ci.yml
name: CI

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: wikidb
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
      - uses: actions/checkout@v2
      - uses: actions/setup-node@v2
        with:
          node-version: '16'
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.0'
          extensions: mbstring, xml, json, intl
          coverage: xdebug
      - name: Install dependencies
        run: |
          composer install --prefer-dist --no-progress
          npm ci
      - name: Run tests
        run: composer test
```

## Browser Compatibility

### Supported Browsers
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

### Testing Matrix
```yaml
# .github/workflows/browser-tests.yml
strategy:
  matrix:
    browser: [chrome, firefox, safari]
    os: [windows-latest, macos-latest, ubuntu-latest]
```

## Performance Testing

### Metrics
- Time to First Byte (TTFB)
- First Contentful Paint (FCP)
- Time to Interactive (TTI)
- Memory usage

### Tools
- WebPageTest
- Lighthouse
- Blackfire.io

## Security Testing

### Scans
- Dependency vulnerabilities: `npm audit`, `composer audit`
- Static analysis: PHPStan, SonarQube
- Dynamic analysis: OWASP ZAP

## Test Reporting

### Reports
- JUnit XML for CI integration
- HTML coverage reports
- Screenshots on test failure
- Video recordings for E2E tests

### Monitoring
- Track test flakiness
- Monitor test duration
- Alert on performance regressions

## Version History
- **0.3.1**: Initial version
