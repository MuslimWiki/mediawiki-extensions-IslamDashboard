# IslamDashboard Extension Structure

This document outlines the file and folder structure of the IslamDashboard extension, explaining the purpose of each directory and file.

## Root Directory Structure

```
extensions/IslamDashboard/
├── docs/                    # Documentation files
├── i18n/                    # Internationalization files
├── includes/                # Core PHP classes
│   ├── Api/                 # API modules
│   ├── Hooks/               # Hook handlers
│   ├── Special/             # Special pages
│   └── Widgets/             # Widget implementations
├── resources/               # Frontend resources
│   ├── modules/             # General JavaScript modules (deprecated, use widgets/)
│   └── widgets/             # Widget-specific frontend resources
├── templates/               # Server-side templates
│   └── widgets/             # Widget-specific templates
├── tests/                   # Test files
├── IslamDashboard.alias.php # Special page aliases
├── IslamDashboard.php       # Extension entry point
├── extension.json           # Extension manifest
└── README.md                # Basic documentation
```

## Detailed Directory Descriptions

### 1. `/docs`
Contains all documentation files including:
- `API_REFERENCE.md`: API documentation
- `ARCHITECTURE.md`: System architecture overview
- `STRUCTURE.md`: This file - explains the directory structure
- Other documentation files

### 2. `/i18n`
Internationalization files for different languages. Each language has its own JSON file (e.g., `en.json`, `ar.json`).

### 3. `/includes`
Core PHP classes organized by functionality:

#### `/includes/Api`
API modules that handle AJAX requests and other API interactions.

#### `/includes/Hooks`
Hook handlers that integrate with MediaWiki's hook system.

#### `/includes/Special`
Special page implementations, including the main dashboard page.

#### `/includes/Widgets`
PHP classes for dashboard widgets. Each widget should be in its own file following the naming convention `WidgetNameWidget.php`.

### 4. `/resources`
Frontend assets (JavaScript, CSS, images, etc.).

#### `/resources/widgets` (Recommended)
Widget-specific frontend resources. Each widget should have its own subdirectory or follow the naming pattern `widgets/WidgetName.{js,less}`.

> **Note**: The `/resources/modules/widgets` directory is deprecated. All widget assets should be moved to `/resources/widgets`.

### 5. `/templates`
Server-side templates using MediaWiki's template system.

#### `/templates/widgets`
Templates for individual widgets. Each widget should have its own template file following the naming convention `WidgetName.mustache`.

### 6. `/tests`
Test files including PHPUnit and QUnit tests.

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
   - Place PHP classes in `/includes/Widgets`
   - Put frontend assets in `/resources/widgets`
   - Store templates in `/templates/widgets`

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
├── includes/Widgets/
│   └── WelcomeWidget.php     # Widget PHP class
├── resources/widgets/
│   ├── welcome-widget.js     # Widget JavaScript
│   └── welcome-widget.less   # Widget styles
└── templates/widgets/
    └── Welcome.mustache      # Widget template
```
