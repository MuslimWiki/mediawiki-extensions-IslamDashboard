# IslamDashboard Development Guide

This document provides information for developers who want to contribute to the IslamDashboard extension.

## Table of Contents

- [Development Environment Setup](#development-environment-setup)
- [Code Organization](#code-organization)
- [Adding New Widgets](#adding-new-widgets)
- [JavaScript Architecture](#javascript-architecture)
- [PHP API](#php-api)
- [Testing](#testing)
- [Code Style](#code-style)
- [Submitting Changes](#submitting-changes)
- [Release Process](#release-process)

## Development Environment Setup

1. **Prerequisites**
   - PHP 7.4+
   - MediaWiki 1.43+
   - Node.js 14+ (for frontend development)
   - npm or yarn

2. **Clone the repository**
   ```bash
   git clone https://github.com/muslim-wiki/IslamDashboard.git
   cd IslamDashboard
   ```

3. **Install dependencies**
   ```bash
   # Install PHP dependencies (via Composer)
   composer install
   
   # Install frontend dependencies
   npm install
   ```

4. **Link to MediaWiki installation**
   ```bash
   ln -s $(pwd) /path/to/mediawiki/extensions/IslamDashboard
   ```

5. **Enable the extension**
   Add the following to your `LocalSettings.php`:
   ```php
   wfLoadExtension( 'IslamDashboard' );
   ```

## Code Organization

```
extensions/IslamDashboard/
├── docs/                  # Documentation
├── i18n/                  # Internationalization files
├── includes/              # PHP classes and interfaces
│   └── Api/               # API modules
├── resources/
│   ├── modules/          # JavaScript modules
│   └── styles/           # CSS/LESS styles
├── tests/                # Test suites
├── Hooks.php            # Hook handlers
├── IslamDashboard.php   # Extension setup
└── SpecialDashboard.php # Special page implementation
```

## Adding New Widgets

1. **Create a Widget Class**
   Create a new class in `includes/Widgets/` that extends `DashboardWidget`:

   ```php
   <?php
   namespace MediaWiki\Extension\IslamDashboard\Widgets;

   class MyCustomWidget extends DashboardWidget {
       public function __construct() {
           parent::__construct(
               'my-custom-widget',
               'mycustomwidget-title',
               'mycustomwidget-desc'
           );
       }

       public function getContent() {
           return '<div class="my-custom-widget">Hello, world!</div>';
       }
   }
   ```

2. **Register the Widget**
   Register your widget with the WidgetManager. The best place to do this is in your extension's setup file (e.g., `extension.json` or `IslamDashboard.php`). Here's how to register a widget:

   ```php
   // Get the WidgetManager instance
   $widgetManager = \MediaWiki\Extension\IslamDashboard\WidgetManager::getInstance();
   
   // Register your widget
   $widgetManager->registerWidget(new \MediaWiki\Extension\IslamDashboard\Widgets\MyCustomWidget());
   ```

   For core widgets, you can add them to the `registerCoreWidgets()` method in the `WidgetManager` class:

   ```php
   private function registerCoreWidgets() {
       $this->registerWidget(new Widgets\WelcomeWidget());
       $this->registerWidget(new Widgets\RecentActivityWidget());
       $this->registerWidget(new Widgets\QuickActionsWidget());
       // Add your custom widget here
       $this->registerWidget(new Widgets\MyCustomWidget());
   }
   ```

3. **Add Translations**
   Add translations for your widget's title and description to the `i18n/` files.

## JavaScript Architecture

The frontend is built using:
- [Vue.js](https://vuejs.org/) for reactive components
- [Codex](https://doc.wikimedia.org/codex/) for UI components
- [webpack](https://webpack.js.org/) for bundling

### Key Files

- `resources/modules/ext.islamDashboard.js` - Main entry point
- `resources/modules/components/` - Vue components
- `resources/modules/store/` - Vuex store for state management
- `resources/modules/api.js` - API client

### Development Workflow

1. Start the development server:
   ```bash
   npm run dev
   ```

2. The build system will watch for changes and automatically rebuild the assets.

## PHP API

The extension provides several API endpoints:

- `action=islamdashboard&subaction=getwidgets` - Get available widgets
- `action=islamdashboard&subaction=savelayout` - Save dashboard layout
- `action=islamdashboard&subaction=hidewidget` - Hide a widget

## Testing

### PHPUnit Tests

Run the PHPUnit tests:

```bash
php ../../tests/phpunit/phpunit.php extensions/IslamDashboard/tests/phpunit/
```

### QUnit Tests

Run the JavaScript tests:

```bash
npm test
```

### Browser Tests

Run the Selenium tests:

```bash
npm run test:browser
```

## Code Style

- PHP: Follow [MediaWiki's PHP code style](https://www.mediawiki.org/wiki/Manual:Coding_conventions/PHP)
- JavaScript: Follow [MediaWiki's JavaScript style guide](https://www.mediawiki.org/wiki/Manual:Coding_conventions/JavaScript)
- CSS: Follow [BEM methodology](http://getbem.com/)

## Submitting Changes

1. Create a new branch for your feature or bugfix:
   ```bash
   git checkout -b feature/my-awesome-feature
   ```

2. Make your changes and commit them:
   ```bash
   git add .
   git commit -m "Add my awesome feature"
   ```

3. Push your changes and create a pull request on GitHub.

## Release Process

1. Update the version number in `extension.json`.
2. Update `RELEASE-NOTES.md` with the changes.
3. Create a tag:
   ```bash
   git tag -a v1.0.0 -m "Version 1.0.0"
   git push origin v1.0.0
   ```
4. Create a new release on GitHub.
5. Update the documentation on mediawiki.org.

## Getting Help

- [Issue tracker](https://github.com/muslim-wiki/IslamDashboard/issues)
- [Discussion forum](https://www.mediawiki.org/wiki/Extension_talk:IslamDashboard)
- [IRC](irc://irc.freenode.net/#mediawiki)

## License

This project is licensed under the GPL-3.0-or-later. See the [LICENSE](LICENSE) file for details.
