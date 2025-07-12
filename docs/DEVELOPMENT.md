# Development Guide - IslamDashboard

## Table of Contents
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Development Setup](#development-setup)
- [Development Environment](#development-environment)
  - [Local Development](#local-development)
  - [Docker Setup](#docker-setup)
  - [IDE Configuration](#ide-configuration)
- [Project Structure](#project-structure)
- [Coding Standards](#coding-standards)
  - [PHP](#php-standards)
  - [JavaScript](#javascript-standards)
  - [CSS/LESS](#cssless-standards)
- [Testing](#testing)
  - [PHPUnit](#phpunit)
  - [QUnit](#qunit)
  - [Browser Tests](#browser-tests)
  - [Code Coverage](#code-coverage)
- [Debugging](#debugging)
  - [Xdebug](#xdebug)
  - [Browser Developer Tools](#browser-developer-tools)
  - [Logging](#logging)
- [Version Control](#version-control)
  - [Branching Strategy](#branching-strategy)
  - [Commit Messages](#commit-messages)
- [Code Review](#code-review)
  - [Pull Request Process](#pull-request-process)
  - [Review Guidelines](#review-guidelines)
- [Documentation](#documentation)
- [Release Process](#release-process)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
  - [How to Contribute](#how-to-contribute)
  - [Feature Development](#feature-development)
  - [Bug Fixes](#bug-fixes)
  - [Documentation Updates](#documentation-updates)

## Getting Started

### Prerequisites

#### Required
- PHP 8.0+ (8.1+ recommended)
- Composer 2.0+
- Node.js 16+ and npm 8+ (for frontend development)
- MediaWiki 1.43+
- Git 2.20+

#### Recommended
- PHP extensions: xdebug, intl, mbstring, json, curl, gd
- Docker and Docker Compose (for containerized development)
- PHP_CodeSniffer with MediaWiki coding standards
- ESLint and Stylelint for frontend code quality

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-org/IslamDashboard.git extensions/IslamDashboard
   cd extensions/IslamDashboard
   ```

2. Install PHP dependencies:
   ```bash
   composer install --prefer-dist --ignore-platform-reqs
   ```

3. Install frontend dependencies:
   ```bash
   npm ci
   ```

4. Build assets:
   ```bash
   # Development build with source maps
   npm run dev
   
   # Production build (minified)
   npm run build
   ```

5. Enable the extension in `LocalSettings.php`:
   ```php
   wfLoadExtension( 'IslamDashboard' );
   
   // Development configuration
   $wgIslamDashboardConfig = [
       'debug' => true,
       'enableDevTools' => true
   ];
   ```

### Development Setup

1. **Set up pre-commit hooks** (optional but recommended):
   ```bash
   # Install pre-commit hooks
   npm run prepare
   ```

2. **Configure your IDE/Editor**:
   - Install recommended extensions for PHP, JavaScript, and MediaWiki development
   - Set up PHP_CodeSniffer and ESLint integration
   - Configure editor formatting to match project standards

3. **Verify your setup**:
   ```bash
   # Run PHP linter
   composer lint
   
   # Run JavaScript linter
   npm run lint
   
   # Run tests
   composer test
   ```

## Development Environment

### Local Development

For local development, you can use:

1. **MediaWiki-Vagrant** (recommended):
   ```bash
   # Install MediaWiki-Vagrant
   git clone https://gerrit.wikimedia.org/r/mediawiki/vagrant
   cd vagrant
   
   # Add IslamDashboard as a dependency
   echo 'mediawiki/extensions/IslamDashboard' >> extensions.txt
   
   # Start the environment
   vagrant up
   ```

2. **Manual Setup**:
   - Install a LAMP/LEMP stack
   - Set up a MediaWiki instance
   - Clone the extension into the extensions directory
   - Configure your web server

### Docker Setup

A `docker-compose.yml` file is provided for containerized development:

```bash
# Start containers
docker-compose up -d

# Install dependencies
docker-compose exec app composer install
docker-compose exec app npm ci

# Run tests
docker-compose exec app composer test

# Access the site at http://localhost:8080
```

### IDE Configuration

#### PHPStorm/IntelliJ

1. Install plugins:
   - PHP
   - PHP Annotations
   - PHP Toolbox
   - PHPUnit
   - MediaWiki
   - ESLint
   - Stylelint

2. Configure PHP interpreter:
   - Go to Settings → Languages & Frameworks → PHP
   - Add a new PHP interpreter (local or Docker)
   - Set PHP language level to 8.0+

3. Configure code style:
   - Go to Settings → Editor → Code Style → PHP
   - Import the `.phpcs.xml` ruleset
   - Set line length to 120

#### VS Code

1. Install extensions:
   - PHP Intelephense
   - PHP Debug
   - PHP CS Fixer
   - ESLint
   - Stylelint
   - MediaWiki extension

2. Recommended settings (`.vscode/settings.json`):
   ```json
   {
       "php.validate.executablePath": "/path/to/php",
       "php.suggest.basic": false,
       "intelephense.environment.phpVersion": "8.0.0",
       "intelephense.files.maxSize": 5000000,
       "editor.formatOnSave": true,
       "editor.codeActionsOnSave": {
           "source.fixAll.eslint": true,
           "source.fixAll.stylelint": true
       },
       "eslint.validate": ["javascript"],
       "stylelint.validate": ["css", "less", "scss"]
   }
   ```

## Project Structure

```
extensions/IslamDashboard/
├── docs/                    # Documentation files
├── i18n/                    # Internationalization files
├── includes/                # PHP classes
│   ├── Api/                 # API endpoints
│   ├── Hooks/               # Hook handlers
│   ├── Navigation/          # Navigation system
│   └── Widgets/             # Widget implementations
├── maintenance/             # Maintenance scripts
├── resources/               # Frontend resources
│   ├── lib/                 # Third-party libraries
│   ├── modules/             # Resource modules
│   ├── styles/              # CSS/LESS styles
│   └── widgets/             # Widget-specific resources
├── scripts/                 # Build and utility scripts
├── templates/               # PHP and Mustache templates
├── tests/                   # Test files
│   ├── phpunit/             # PHPUnit tests
│   └── qunit/               # QUnit tests
├── .editorconfig            # Editor configuration
├── .eslintrc.js             # ESLint configuration
├── .gitattributes           # Git attributes
├── .gitignore               # Git ignore rules
├── .phpcs.xml               # PHP_CodeSniffer rules
├── .stylelintrc             # Stylelint configuration
├── composer.json            # PHP dependencies
├── extension.json           # Extension manifest
├── package.json             # Node.js dependencies
└── README.md                # Project overview
```

## Coding Standards

### PHP Standards

1. **Follow MediaWiki's PHP coding conventions**:
   - [PHP coding conventions](https://www.mediawiki.org/wiki/Manual:Coding_conventions/PHP)
   - [PHP documentation standards](https://www.mediawiki.org/wiki/Manual:PHP_documentation)

2. **Type hints and return types**:
   ```php
   /**
    * Get a user by ID.
    *
    * @param int $userId User ID
    * @return User|null User object or null if not found
    */
   public function getUserById( int $userId ): ?User {
       // ...
   }
   ```

3. **Error handling**:
   - Use specific exception types
   - Include descriptive error messages
   - Log errors appropriately
   - Never expose sensitive information

### JavaScript Standards

1. **Follow MediaWiki's JavaScript guidelines**:
   - [JavaScript guidelines](https://www.mediawiki.org/wiki/Manual:Coding_conventions/JavaScript)
   - [jQuery guidelines](https://www.mediawiki.org/wiki/jQuery)

2. **Modern JavaScript features**:
   - Use ES6+ syntax
   - Prefer `const` and `let` over `var`
   - Use arrow functions where appropriate
   - Use template literals for string interpolation

3. **Example**:
   ```javascript
   /**
    * Updates the widget content.
    *
    * @param {Object} data Widget data
    * @return {jQuery.Promise} Promise that resolves when update is complete
    */
   function updateWidget( data ) {
       return $.ajax( {
           url: mw.util.wikiScript( 'api' ),
           data: {
               action: 'islamdashboard-update-widget',
               data: JSON.stringify( data ),
               format: 'json',
               token: mw.user.tokens.get( 'csrfToken' )
           },
           method: 'POST',
           dataType: 'json'
       } );
   }
   ```

### CSS/LESS Standards

1. **Follow MediaWiki's CSS guidelines**:
   - [CSS guidelines](https://www.mediawiki.org/wiki/Manual:Coding_conventions/CSS)
   - [LESS guidelines](https://www.mediawiki.org/wiki/LESS)

2. **Naming conventions**:
   - Use BEM (Block Element Modifier) methodology
   - Prefix classes with `ext-islamdashboard-`
   - Use kebab-case for class names

3. **Example**:
   ```less
   .ext-islamdashboard-widget {
       &__header {
           background-color: #f8f9fa;
           padding: 1rem;
           
           &--collapsed {
               padding: 0.5rem;
           }
       }
       
       &__title {
           font-size: 1.2em;
           margin: 0;
       }
   }
   ```

## Testing

### PHPUnit

#### Running Tests

```bash
# Run all PHPUnit tests
composer test:phpunit

# Run a specific test file
composer test:phpunit -- tests/phpunit/unit/Widgets/WidgetTest.php

# Run tests with coverage (requires Xdebug or PCOV)
composer test:coverage
```

#### Writing Tests

1. **Test structure**:
   ```php
   <?php
   
   namespace MediaWiki\Extension\IslamDashboard\Tests\Unit\Widgets;
   
   use MediaWiki\Extension\IslamDashboard\Widgets\WelcomeWidget;
   use MediaWikiIntegrationTestCase;
   
   /**
    * @covers \MediaWiki\Extension\IslamDashboard\Widgets\WelcomeWidget
    * @group IslamDashboard
    */
   class WelcomeWidgetTest extends MediaWikiIntegrationTestCase {
       
       protected function setUp(): void {
           parent::setUp();
           // Set up test data
       }
       
       public function testGetContent() {
           $widget = new WelcomeWidget();
           $content = $widget->getContent();
           
           $this->assertStringContainsString( 'Welcome', $content );
       }
   }
   ```

2. **Test data**:
   - Use `MediaWikiIntegrationTestCase::setUp()` for common setup
   - Create test users with appropriate permissions
   - Use mock objects when necessary

### QUnit

#### Running Tests

```bash
# Run QUnit tests in Node.js
npm test

# Run QUnit tests in browser
npm run test:browser
```

#### Writing Tests

1. **Test structure**:
   ```javascript
   /* eslint-disable no-jquery/no-global-selector */
   QUnit.module( 'ext.islamDashboard.WidgetManager', QUnit.newMwEnvironment( {
       beforeEach: function () {
           // Set up test environment
           this.widgetManager = new mw.islamDashboard.WidgetManager();
       }
   } ) );
   
   QUnit.test( 'registerWidget', function ( assert ) {
       const testWidget = {
           id: 'test-widget',
           title: 'Test Widget',
           template: '<div>Test</div>'
       };
       
       this.widgetManager.registerWidget( testWidget );
       const widget = this.widgetManager.getWidget( 'test-widget' );
       
       assert.strictEqual( widget.id, 'test-widget', 'Widget is registered' );
   } );
   ```

### Browser Tests

1. **Using Selenium/WebDriver**:
   ```bash
   # Install browser drivers
   npx selenium-standalone install
   
   # Start Selenium server
   npx selenium-standalone start
   
   # Run browser tests
   npm run test:browser
   ```

2. **Writing browser tests**:
   - Test user interactions
   - Verify DOM changes
   - Test responsive behavior

### Code Coverage

1. **PHP Coverage**:
   ```bash
   # Generate coverage report
   composer test:coverage
   
   # View HTML report (after running coverage)
   open coverage/index.html
   ```

2. **JavaScript Coverage**:
   ```bash
   # Generate coverage report
   npm run coverage
   
   # View HTML report
   open coverage/lcov-report/index.html
   ```

## Debugging

### Xdebug

1. **Configuration** (in `php.ini`):
   ```ini
   zend_extension=xdebug.so
   xdebug.mode=debug
   xdebug.start_with_request=yes
   xdebug.client_port=9003
   xdebug.client_host=host.docker.internal  # For Docker
   xdebug.idekey=PHPSTORM
   ```

2. **Debugging in PHPStorm**:
   - Set up a PHP Remote Debug configuration
   - Set breakpoints in your code
   - Start listening for PHP Debug connections
   - Trigger the code path you want to debug

### Browser Developer Tools

1. **Chrome/Firefox DevTools**:
   - Inspect network requests
   - Debug JavaScript
   - Profile performance
   - Test responsive layouts

2. **MediaWiki Debug Mode**:
   ```php
   // In LocalSettings.php
   $wgDebugToolbar = true;
   $wgShowSQLErrors = true;
   $wgShowDBErrorBacktrace = true;
   $wgDevelopmentWarnings = true;
   ```

### Logging

1. **PHP Logging**:
   ```php
   // Log a debug message
   wfDebugLog( 'IslamDashboard', 'Widget initialized', 'private' );
   
   // Log with context
   wfDebugLog( 'IslamDashboard', 'User {user} accessed dashboard', 'all', [
       'user' => $user->getName(),
       'ip' => $request->getIP()
   ] );
   ```

2. **JavaScript Logging**:
   ```javascript
   // Debug logging (only in debug mode)
   mw.log( 'Widget initialized', data );
   
   // Error logging
   mw.track( 'error.islamdashboard', {
       error: 'Failed to load widget',
       details: error
   } );
   ```

## Version Control

### Branching Strategy

1. **Main Branches**:
   - `main`: Production-ready code
   - `develop`: Integration branch for features

2. **Feature Branches**:
   - `feature/feature-name`: New features
   - `bugfix/issue-123`: Bug fixes
   - `docs/update-readme`: Documentation updates

3. **Release Branches**:
   - `release/1.0.0`: Release preparation

### Commit Messages

Follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

**Types**:
- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation only changes
- `style`: Changes that do not affect the meaning of the code
- `refactor`: A code change that neither fixes a bug nor adds a feature
- `perf`: A code change that improves performance
- `test`: Adding missing tests or correcting existing tests
- `chore`: Changes to the build process or auxiliary tools

**Example**:
```
feat(widgets): add new calendar widget

Add a calendar widget that shows upcoming events from the wiki.

Closes #123
```

## Code Review

### Pull Request Process

1. **Before Submitting**:
   - Ensure all tests pass
   - Update documentation if needed
   - Follow the coding standards
   - Add tests for new features

2. **Creating a PR**:
   - Reference any related issues
   - Provide a clear description of changes
   - Include screenshots for UI changes
   - Update CHANGELOG.md

3. **Review Process**:
   - At least one approval required
   - All CI checks must pass
   - Address all review comments
   - Squash commits when merging

### Review Guidelines

1. **Checklist**:
   - [ ] Code follows standards
   - [ ] Tests are included
   - [ ] Documentation is updated
   - [ ] No security issues
   - [ ] Performance impact considered

2. **Leaving Comments**:
   - Be constructive and specific
   - Suggest improvements
   - Acknowledge good practices

## Documentation

1. **Code Documentation**:
   - PHPDoc for all classes, methods, and properties
   - Inline comments for complex logic
   - Type hints and return types

2. **User Documentation**:
   - Keep README.md up to date
   - Document configuration options
   - Include examples

3. **API Documentation**:
   - Document all public APIs
   - Include usage examples
   - Document parameters and return values

## Release Process

1. **Versioning**:
   - Follow [Semantic Versioning](https://semver.org/)
   - Update version in `extension.json`
   - Update `CHANGELOG.md`

2. **Pre-release Checks**:
   - Run all tests
   - Verify documentation
   - Check dependencies

3. **Creating a Release**:
   ```bash
   # Update version
   npm version patch  # or minor, major
   
   # Build production assets
   npm run build
   
   # Create release commit
   git add .
   git commit -m "chore(release): v$(node -p "require('./package.json').version)"
   
   # Create tag
   git tag -a v$(node -p "require('./package.json').version)" -m "Version $(node -p "require('./package.json').version)"
   
   # Push changes
   git push origin main --follow-tags
   ```

4. **Post-release**:
   - Create release on GitHub
   - Update documentation
   - Announce the release

## Troubleshooting

### Common Issues

1. **Extension not loading**:
   - Check PHP error log
   - Verify `extension.json` is valid
   - Check file permissions

2. **Assets not loading**:
   - Run `npm run build`
   - Check browser console for 404 errors
   - Clear MediaWiki cache

3. **Database issues**:
   - Run `php maintenance/update.php`
   - Check database permissions
   - Verify table structure

4. **JavaScript errors**:
   - Check browser console
   - Run `npm run lint`
   - Verify dependencies

## Contributing

### How to Contribute

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

### Feature Development

1. **Proposing a Feature**:
   - Open an issue to discuss the feature
   - Get feedback before implementation
   - Document the proposed changes

2. **Implementation**:
   - Follow the coding standards
   - Write tests
   - Update documentation

### Bug Fixes

1. **Reporting Bugs**:
   - Check existing issues
   - Provide steps to reproduce
   - Include environment details

2. **Fixing Bugs**:
   - Write a test that reproduces the bug
   - Fix the issue
   - Add regression tests

### Documentation Updates

1. **Improving Documentation**:
   - Fix typos and errors
   - Add examples
   - Improve clarity

2. **Translation**:
   - Update i18n files
   - Follow MediaWiki's translation guidelines

### Code of Conduct

Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in this project you agree to abide by its terms.

### License

This project is licensed under the [GNU General Public License v3.0](LICENSE).

---

*Last updated: $(date +%Y-%m-%d)*
   // Load the extension
   wfLoadExtension( 'IslamDashboard' );
   
   // Development settings (optional)
   $wgShowExceptionDetails = true;
   $wgShowDBErrorBacktrace = true;
   $wgDebugToolbar = true;
   ```

## Development Environment

### Recommended Tools
- **Code Editors:**
  - [PHPStorm](https://www.jetbrains.com/phpstorm/) (Recommended)
  - [VS Code](https://code.visualstudio.com/) with PHP and MediaWiki extensions
- **Debugging:**
  - [Xdebug](https://xdebug.org/) for PHP debugging
  - Browser developer tools (Chrome DevTools, Firefox Developer Tools)
- **Code Quality:**
  - [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
  - [ESLint](https://eslint.org/) for JavaScript
  - [StyleLint](https://stylelint.io/) for CSS/Less
- **Version Control:**
  - Git
  - GitHub Desktop (for GUI users)

### Development Workflow

1. **Start a new feature or bugfix:**
   ```bash
   git checkout main
   git pull
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes** following the coding standards

3. **Run tests** (see [Testing](#testing) section)

4. **Commit your changes** with a descriptive message:
   ```bash
   git add .
   git commit -m "Add your feature description"
   ```

5. **Push your changes** and create a pull request

## Project Structure

```
extensions/IslamDashboard/
├── docs/                    # Documentation files
├── i18n/                    # Internationalization files
├── includes/                # PHP classes
│   ├── Api/                 # API modules
│   ├── Hooks/               # Hook handlers
│   ├── Navigation/          # Navigation system
│   └── Widgets/             # Widget implementations
├── resources/               # Frontend assets
│   ├── modules/             # JavaScript modules
│   ├── styles/              # CSS/Less styles
│   └── widgets/             # Widget-specific assets
├── scripts/                 # Maintenance and utility scripts
├── templates/               # HTML templates
│   └── widgets/             # Widget templates
├── tests/                   # Test suites
│   ├── phpunit/             # PHPUnit tests
│   └── qunit/               # QUnit tests
├── vendor/                  # Composer dependencies
├── .editorconfig            # Editor configuration
├── .eslintrc.js             # ESLint configuration
├── .gitignore               # Git ignore rules
├── .phpcs.xml               # PHP_CodeSniffer rules
├── composer.json            # PHP dependencies
├── extension.json           # Extension metadata
├── Gruntfile.js             # Build tasks
├── package.json             # Node.js dependencies
└── README.md                # Project overview
```

## Coding Standards

### PHP
- Follow [MediaWiki's PHP coding conventions](https://www.mediawiki.org/wiki/Manual:Coding_conventions/PHP)
- Use type hints and return type declarations where possible
- Document all classes, methods, and properties with PHPDoc
- Keep methods small and focused (max 20-30 lines)
- Use dependency injection where appropriate

### JavaScript
- Follow [MediaWiki's JavaScript coding conventions](https://www.mediawiki.org/wiki/Manual:Coding_conventions/JavaScript)
- Use ES6+ features (transpiled for browser compatibility)
- Use JSDoc for documentation
- Prefer promises over callbacks
- Use jQuery for DOM manipulation when needed

### CSS/Less
- Follow [MediaWiki's CSS coding conventions](https://www.mediawiki.org/wiki/Manual:Coding_conventions/CSS)
- Use semantic class names
- Follow BEM methodology for complex components
- Use CSS variables for theming
- Keep selectors shallow (max 3 levels deep)

## Testing

### PHPUnit Tests

Run all PHPUnit tests:
```bash
composer test:phpunit
```

Run a specific test file:
```bash
composer test:phpunit -- tests/phpunit/Unit/Widgets/WidgetTest.php
```

### QUnit Tests

Run all QUnit tests:
```bash
npm test
```

Run tests in watch mode:
```bash
npm run test:watch
```

### Code Coverage

Generate code coverage report:
```bash
composer test:coverage
```

## Debugging

### PHP Debugging

1. Set up Xdebug in your IDE
2. Add breakpoints in your code
3. Start a debugging session in your IDE
4. Trigger the code path in your browser

### Browser Debugging

1. Open browser developer tools (F12)
2. Use the Console tab for JavaScript errors
3. Use the Network tab for API requests
4. Use the Elements tab for DOM inspection

### Debug Logging

Add debug logging in PHP:
```php
wfDebugLog( 'IslamDashboard', 'Debug message' );
```

View debug logs in `debug.log` (configured in `LocalSettings.php`):
```php
$wgDebugLogFile = "$IP/debug.log";
```

## Version Control

### Branching Strategy
- `main`: Stable, production-ready code
- `feature/*`: New features
- `bugfix/*`: Bug fixes
- `release/*`: Release preparation

### Commit Messages

Follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

Example:
```
feat(widgets): add new weather widget

Add a weather widget that displays current weather information based on user location.

Closes #123
```

## Code Review

### Before Submitting a Pull Request
- [ ] All tests pass
- [ ] Code follows coding standards
- [ ] Documentation is updated
- [ ] New tests are added for new features
- [ ] No commented-out code
- [ ] No console errors

### Review Checklist
- [ ] Code is clean and maintainable
- [ ] No security vulnerabilities
- [ ] Proper error handling
- [ ] Performance considerations
- [ ] Cross-browser compatibility

## Documentation

### Updating Documentation
1. Update the relevant `.md` files in the `docs/` directory
2. Update PHPDoc comments in code
3. Update inline code comments if needed
4. Update README.md if necessary

### Documentation Standards
- Use Markdown for all documentation
- Keep lines under 100 characters
- Use proper heading hierarchy
- Include code examples where helpful
- Keep documentation up-to-date with code changes

## Release Process

### Versioning

Follow [Semantic Versioning](https://semver.org/):
- MAJOR: Breaking changes
- MINOR: New features (backward-compatible)
- PATCH: Bug fixes (backward-compatible)

### Release Steps

1. Update version in `extension.json`
2. Update `CHANGELOG.md`
3. Create a release branch:
   ```bash
   git checkout -b release/vX.Y.Z
   ```
4. Build production assets:
   ```bash
   npm run build
   ```
5. Commit changes:
   ```bash
   git add .
   git commit -m "chore(release): vX.Y.Z"
   ```
6. Create a tag:
   ```bash
   git tag -a vX.Y.Z -m "Version X.Y.Z"
   ```
7. Push changes:
   ```bash
   git push origin release/vX.Y.Z
   git push --tags
   ```
8. Create a release on GitHub
9. Merge to main:
   ```bash
   git checkout main
   git merge --no-ff release/vX.Y.Z
   git push origin main
   ```

## Troubleshooting

### Common Issues

#### Widgets not loading
1. Check browser console for errors
2. Verify widget registration in `extension.json`
3. Check user permissions
4. Clear browser cache

#### JavaScript errors
1. Check for syntax errors
2. Verify all dependencies are loaded
3. Check browser compatibility

#### PHP errors
1. Check PHP error log
2. Verify PHP version compatibility
3. Check for missing dependencies

### Getting Help

1. Check the [GitHub issues](https://github.com/your-org/IslamDashboard/issues)
2. Ask in the #mediawiki IRC channel
3. Contact the maintainers

## Contributing

We welcome contributions! Here's how you can help:

1. Report bugs by [opening an issue](https://github.com/your-org/IslamDashboard/issues/new)
2. Suggest new features
3. Submit pull requests
4. Improve documentation
5. Help with testing

### Code of Conduct

Please follow our [Code of Conduct](CODE_OF_CONDUCT.md) in all interactions.
extensions/IslamDashboard/
├── docs/                  # Documentation
├── i18n/                  # Internationalization files
├── includes/              # PHP classes
│   └── Widgets/           # Widget implementations
├── resources/             # Frontend resources
│   ├── modules/          # JavaScript modules
│   └── styles/           # CSS/LESS files
├── tests/                # Test files
├── extension.json        # Extension manifest
└── IslamDashboard.php    # Extension entry point
```

### Development Server
Use MediaWiki's built-in development server:
```bash
php -S 0.0.0.0:8080 -t /path/to/mediawiki
```

## Coding Standards

### PHP
- Follow [MediaWiki's PHP coding conventions](https://www.mediawiki.org/wiki/Manual:Coding_conventions/PHP)
- Use type hints where possible
- Document all public methods with PHPDoc
- Keep classes focused and small

### JavaScript
- Use ES6+ syntax
- Follow [MediaWiki's JavaScript coding conventions](https://www.mediawiki.org/wiki/Manual:Coding_conventions/JavaScript)
- Use JSDoc for documentation
- Prefer promises over callbacks

### CSS/LESS
- Follow [BEM methodology](http://getbem.com/)
- Use Codex design tokens when possible
- Keep selectors shallow and specific
- Use CSS variables for theming

## Testing

### PHPUnit Tests
Run the test suite:
```bash
php ../../tests/phpunit/phpunit.php --group IslamDashboard
```

### QUnit Tests
Run JavaScript tests:
```bash
npm test
```

### Browser Tests
1. Install Selenium WebDriver
2. Run the test suite:
   ```bash
   npm run browser-test
   ```

### Linting
Check code style:
```bash
# PHP
composer lint

# JavaScript
npm run lint:js

# CSS
npm run lint:css
```

## Version Control

### Branching Strategy
- `main`: Stable, production-ready code
- `feature/*`: New features
- `bugfix/*`: Bug fixes
- `release/*`: Release preparation

### Commit Messages
Follow [Conventional Commits](https://www.conventionalcommits.org/):
```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

Example:
```
feat(widgets): add new calendar widget

Add a calendar widget that shows upcoming events.

Closes #123
```

## Code Review

### Process
1. Create a pull request
2. Assign reviewers
3. Address feedback
4. Get approval
5. Squash and merge

### Review Checklist
- [ ] Code follows style guidelines
- [ ] Tests are included
- [ ] Documentation is updated
- [ ] No security issues
- [ ] Performance impact is considered

## Documentation

### Updating Documentation
1. Update the relevant `.md` files in the `docs/` directory
2. Update code comments if necessary
3. Update inline documentation

### Generating API Docs
```bash
composer doc
```

## Release Process

### Versioning
Follow [Semantic Versioning](https://semver.org/):
- MAJOR: Breaking changes
- MINOR: New features (backward-compatible)
- PATCH: Bug fixes (backward-compatible)

### Steps
1. Update version in `extension.json`
2. Update `CHANGELOG.md`
3. Create a release branch
4. Test thoroughly
5. Create a release tag
6. Push to repository

## Troubleshooting

### Common Issues

#### Extension Not Loading
- Check error logs
- Verify file permissions
- Clear MediaWiki cache

#### Widgets Not Appearing
- Check browser console for errors
- Verify widget registration
- Check user permissions

#### Styling Issues
- Clear browser cache
- Check for CSS conflicts
- Verify LESS compilation

### Getting Help
- Check the [issue tracker](https://github.com/your-org/IslamDashboard/issues)
- Ask in the #islamdashboard channel
- File a bug report

## See Also
- [Widget Development Guide](./WIDGETS.md)
- [API Reference](./API_REFERENCE.md)
- [Configuration Guide](./CONFIGURATION.md)
- [Architecture](./ARCHITECTURE.md)
