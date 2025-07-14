# IslamDashboard Codebase Structure

This document outlines the organization of the IslamDashboard extension, following MediaWiki's best practices and PSR-4 autoloading standards.

## Directory Structure

```
extensions/IslamDashboard/
├── docs/                    # Documentation (see Documentation section below)
├── i18n/                    # Internationalization files
│   ├── en.json              # English translations
│   └── qqq.json             # Message documentation
├── includes/                # Legacy includes (to be deprecated)
│   └── ...
├── maintenance/             # Maintenance scripts
│   ├── sql/                 # Database patches
│   └── updateWidgets.php    # Widget update script
├── resources/               # Frontend resources
│   ├── modules/             # JavaScript modules
│   ├── styles/              # Global styles
│   └── widgets/             # Widget-specific resources
├── src/                     # PHP source code (PSR-4)
│   ├── Api/                 # API modules
│   ├── Hooks/               # Hook handlers
│   ├── Maintenance/         # Maintenance scripts
│   ├── Special/             # Special pages
│   ├── Widgets/             # Widget implementations
│   └── IslamDashboard.php   # Extension entry point
├── templates/               # Server-side templates
│   └── widgets/             # Widget-specific templates
├── tests/                   # Test files
│   ├── phpunit/             # PHPUnit tests
│   ├── qunit/               # QUnit tests
│   └── test_config/         # Test configuration
├── vendor/                  # Composer dependencies
├── .gitignore               # Git ignore rules
├── composer.json            # Composer configuration
├── extension.json           # Extension manifest
├── package.json             # NPM configuration
└── README.md                # Project documentation
```

## Documentation Structure

```
docs/
├── api/                     # API documentation
│   ├── endpoints.md         # Available API endpoints
│   └── reference.md         # Detailed API reference
├── architecture/            # System architecture
│   ├── overview.md          # High-level architecture
│   └── navigation.md        # Navigation system
├── contributing/            # Contribution guidelines
│   ├── code-of-conduct.md   # Community standards
│   └── guide.md             # How to contribute
├── development/             # Development guides
│   ├── guide.md             # Getting started
│   ├── configuration.md     # Configuration options
│   ├── widgets.md           # Widget development
│   ├── testing.md           # Testing guidelines
│   └── versioning.md        # Release process
├── releases/                # Release notes
│   ├── REL0_2_0.md          # Version 0.2.0 notes
│   ├── REL0_2_1.md          # Version 0.2.1 notes
│   └── REL0_2_2.md          # Version 0.2.2 notes
└── structure.md             # This file
```

## PSR-4 Autoloading

The extension uses PSR-4 autoloading for all PHP classes. The base namespace is `MediaWiki\Extension\IslamDashboard`.

### Namespace Structure

```
MediaWiki\Extension\IslamDashboard\
├── Api\                  # API modules
├── Hooks\                # Hook handlers
├── Maintenance\          # Maintenance scripts
├── Special\              # Special pages
└── Widgets\             # Widget implementations
```

### Configuration (composer.json)

```json
{
    "autoload": {
        "psr-4": {
            "MediaWiki\\Extension\\IslamDashboard\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "MediaWiki\\Extension\\IslamDashboard\\Tests\\": "tests/phpunit/"
        }
    }
}
```

## Hook Registration

Hooks are registered in `extension.json` and implemented in the `Hooks` namespace.

### Example Hook Registration

```json
{
    "Hooks": {
        "BeforePageDisplay": "MediaWiki\\Extension\\IslamDashboard\\Hooks::onBeforePageDisplay",
        "LoadExtensionSchemaUpdates": "MediaWiki\\Extension\\IslamDashboard\\Hooks::onLoadExtensionSchemaUpdates"
    }
}
```

### Hook Implementation

```php
namespace MediaWiki\Extension\IslamDashboard\Hooks;

class Hooks {
    public static function onBeforePageDisplay( $out, $skin ) {
        // Hook implementation
    }
}
```

## Resource Loading

Frontend resources are managed through MediaWiki's ResourceLoader.

### Resource Module Definition (extension.json)

```json
{
    "ResourceModules": {
        "ext.islamDashboard": {
            "scripts": ["resources/modules/ext.islamDashboard.js"],
            "styles": ["resources/styles/ext.islamDashboard.less"],
            "dependencies": ["mediawiki.api", "mediawiki.jqueryMsg"],
            "position": "top"
        }
    }
}
```

## Database Schema

Database changes are managed through update scripts in the `maintenance/sql` directory.

### Schema Updates

1. Create a new SQL file in `maintenance/sql/` with the format:
   - `tables.sql` for initial table creation
   - `patch-<description>-<timestamp>.sql` for schema updates

2. Register the update in `Hooks::onLoadExtensionSchemaUpdates`:

```php
public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
    $updater->addExtensionTable(
        'islamdashboard_widgets',
        __DIR__ . '/../sql/tables.sql'
    );
}
```

## Widget Development

Widgets should be placed in the `src/Widgets/` directory and extend the base `DashboardWidget` class.

### Widget Structure

```
src/
└── Widgets/
    ├── DashboardWidget.php          # Base widget class
    ├── WelcomeWidget.php           # Example widget
    └── RecentActivityWidget.php    # Another example widget
```

### Example Widget

```php
namespace MediaWiki\Extension\IslamDashboard\Widgets;

class WelcomeWidget extends DashboardWidget {
    public function getContent() {
        return $this->templateParser->processTemplate('WelcomeWidget', [
            'username' => $this->getUser()->getName()
        ]);
    }
}
```

## Testing

The extension includes both PHPUnit and QUnit tests.

### Running Tests

```bash
# Run PHPUnit tests
composer test:phpunit

# Run QUnit tests
npm test
```

## Maintenance Scripts

Maintenance scripts should be placed in `src/Maintenance/` and extend `\Maintenance`.

### Example Script

```php
namespace MediaWiki\Extension\IslamDashboard\Maintenance;

use Maintenance;

class UpdateWidgets extends Maintenance {
    public function execute() {
        // Script implementation
    }
}
```

## Documentation Guidelines

1. **File Naming**:
   - Use lowercase with hyphens for file names (e.g., `quick-start.md`)
   - Keep file names concise but descriptive
   - Use `.md` extension for Markdown files

2. **Versioning**:
   - Follow [Semantic Versioning](https://semver.org/)
   - Each release gets a `RELx_y_z.md` file in the `releases/` directory
   - Update `CHANGELOG.md` with a summary of changes
   - Update version in `extension.json` and `composer.json`

3. **Code Standards**:
   - Follow [MediaWiki's PHP coding conventions](https://www.mediawiki.org/wiki/Manual:Coding_conventions/PHP)
   - Use type hints and return type declarations where possible
   - Add PHPDoc blocks for all classes, methods, and properties
   - Keep lines under 100 characters
   - Use single quotes for strings unless interpolation is needed

## Testing Structure

The extension includes comprehensive test coverage with PHPUnit for server-side code and QUnit for client-side JavaScript.

### Test Directory Structure

```
tests/
├── phpunit/                 # PHPUnit tests
│   ├── unit/                # Unit tests
│   └── integration/         # Integration tests
├── qunit/                   # QUnit tests
│   ├── tests/               # Test files
│   └── testrunner.html      # Test runner
└── test_config/             # Test configuration
    └── phpunit.xml.dist     # PHPUnit configuration
```

### Writing Tests

#### PHPUnit Example

```php
namespace MediaWiki\Extension\IslamDashboard\Tests\Unit;

use MediaWiki\Extension\IslamDashboard\Widgets\WelcomeWidget;
use MediaWikiIntegrationTestCase;

class WelcomeWidgetTest extends MediaWikiIntegrationTestCase {
    public function testGetContent() {
        $widget = new WelcomeWidget();
        $content = $widget->getContent();
        $this->assertStringContainsString('Welcome', $content);
    }
}
```

#### QUnit Example

```javascript
QUnit.module('ext.islamDashboard', function () {
    QUnit.test('Dashboard initialization', function (assert) {
        const dashboard = new mw.ext.islamDashboard.Dashboard();
        assert.strictEqual(typeof dashboard.init, 'function', 'Dashboard has init method');
    });
});
```

## Internationalization (i18n)

All user-facing text should be internationalized using MediaWiki's message system.

### Message Files

- `i18n/en.json`: English messages
- `i18n/qqq.json`: Message documentation

### Example Message Definition

```json
{
    "@metadata": {
        "authors": ["Your Name"]
    },
    "islamdashboard-welcome-message": "Welcome, $1!",
    "islamdashboard-recent-activity": "Recent Activity"
}
```

### Using Messages in PHP

```php
$msg = wfMessage('islamdashboard-welcome-message')
    ->params($user->getName())
    ->text();
```

### Using Messages in JavaScript

```javascript
mw.message('islamdashboard-welcome-message', username).text();
```

## Maintenance Scripts

Maintenance scripts help with administrative tasks and data migration.

### Creating a Maintenance Script

1. Create a new file in `src/Maintenance/`
2. Extend the `Maintenance` class
3. Implement the `execute()` method
4. Add script documentation

### Example Maintenance Script

```php
namespace MediaWiki\Extension\IslamDashboard\Maintenance;

use Maintenance;
use MediaWiki\MediaWikiServices;

class UpdateWidgetData extends Maintenance {
    public function __construct() {
        parent::__construct();
        $this->addDescription('Update widget data in the database');
        $this->requireExtension('IslamDashboard');
    }

    public function execute() {
        $dbw = $this->getDB(DB_PRIMARY);
        // Update logic here
        $this->output("Widget data updated successfully.\n");
    }
}
```

## Release Process

1. Update version numbers in:
   - `extension.json`
   - `composer.json`
   - `package.json`
2. Update `CHANGELOG.md`
3. Create a release notes file in `docs/releases/`
4. Commit changes with message "Release vX.Y.Z"
5. Create a git tag: `git tag -a vX.Y.Z -m "Version X.Y.Z"`
6. Push changes and tags: `git push && git push --tags`
7. Create a GitHub release with release notes

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature`
5. Submit a pull request

## Code of Conduct

Please read [CODE_OF_CONDUCT.md](contributing/code-of-conduct.md) for details on our code of conduct.

## License

This project is licensed under the GPL-2.0-or-later - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Thanks to all contributors who have helped with this project
- Inspired by various MediaWiki extensions and modern web dashboards

## Key File Descriptions

### `IslamDashboard.php`
The main extension file that handles registration and setup.

### `extension.json`
The extension manifest file that defines:
- Extension metadata
- Required extensions and dependencies
- Resource modules
- Hook registrations
- API modules
- Special pages

### `IslamDashboard.alias.php`
Special page aliases for internationalization.

## Best Practices

1. **Widget Development**:
   - Place PHP classes in `/src/Widgets/` (following PSR-4 autoloading)
   - Put frontend assets in `/resources/widgets/`
   - Store templates in `/templates/widgets/`
   - Widgets will be automatically discovered based on their namespace and location

2. **Naming Conventions**:
   - Use `PascalCase` for PHP class names
   - Use `kebab-case` for file and directory names
   - Prefix widget assets with the widget name (e.g., `welcome-widget.js`)

3. **Deprecation Notice**:
   - The `/resources/modules/widgets` directory is deprecated
   - All widget assets should be moved to `/resources/widgets`
   - Update `extension.json` to reference the new locations

4. **Template Usage**:
   - Use the `renderTemplate()` method in widget classes
   - Keep templates simple and focused on presentation
   - Use the widget name as the template name (e.g., `WelcomeWidget` → `Welcome.mustache`)

## Migration Guide

To update from the old structure to the new structure:

1. Move widget assets from `/resources/modules/widgets` to `/resources/widgets`
2. Update paths in `extension.json`
3. Update any references in PHP and JavaScript files
4. Test all widgets to ensure they still work correctly

## Example Widget Structure

A complete widget implementation includes:

```
extensions/IslamDashboard/
├── src/Widgets/
│   └── WelcomeWidget.php     # Widget PHP class (PSR-4 autoloaded)
├── resources/widgets/
│   ├── welcome-widget.js     # Widget JavaScript
│   └── welcome-widget.less   # Widget styles
└── templates/widgets/
    └── Welcome.mustache      # Widget template
```
