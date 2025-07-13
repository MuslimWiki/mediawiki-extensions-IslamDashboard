# IslamDashboard Documentation Structure

This document outlines the organization of the IslamDashboard documentation and codebase, following the latest standards and best practices.

## Documentation Organization

```
docs/
├── api/                     # API documentation and references
│   └── reference.md         # API reference documentation
├── architecture/            # System architecture documentation
│   ├── overview.md          # High-level architecture overview
│   └── navigation.md        # Navigation system specifications
├── contributing/            # Contribution guidelines
│   ├── code-of-conduct.md   # Community guidelines
│   └── guide.md             # Contribution guide
├── development/             # Development documentation
│   ├── guide.md             # Development guide
│   ├── configuration.md     # Configuration reference
│   └── widgets.md           # Widget development guide
├── releases/                # Release notes and changelog
│   └── REL0_2_1.md          # Versioned release notes
└── structure.md             # This file - documentation structure
```

## Codebase Structure

```
extensions/IslamDashboard/
├── docs/                    # Documentation (see above)
├── i18n/                    # Internationalization files
│   └── en.json              # English translations
├── includes/                # Core PHP classes
│   ├── Api/                 # API modules
│   ├── Hooks/               # Hook handlers
│   ├── Special/             # Special pages
│   └── Widgets/             # Widget implementations
├── resources/               # Frontend resources
│   ├── modules/             # General JavaScript modules
│   └── widgets/             # Widget-specific frontend resources
├── templates/               # Server-side templates
│   └── widgets/             # Widget-specific templates
├── tests/                   # Test files
├── IslamDashboard.alias.php # Special page aliases
├── IslamDashboard.php       # Extension entry point
├── extension.json           # Extension manifest
└── README.md                # Basic documentation
```

## Documentation Guidelines

1. **File Naming**:
   - Use lowercase with hyphens for file names (e.g., `quick-start.md`)
   - Keep file names concise but descriptive
   - Use `.md` extension for Markdown files

2. **Versioning**:
   - Each release gets a `RELx_y_z.md` file in the `releases/` directory
   - Update `CHANGELOG.md` with a summary of changes
   - Update version in `extension.json`

3. **Content Standards**:
   - Use clear, concise language
   - Include code examples where helpful
   - Link to related documentation
   - Keep lines under 100 characters

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
