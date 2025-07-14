# IslamDashboard Reference Guide

## Version: 0.3.1

## Table of Contents
1. [Configuration Variables](#configuration-variables)
2. [IslamSkin Configuration](#islamsin-configuration)
3. [Global Variables](#global-variables)
4. [Core Classes](#core-classes)
5. [Hook Reference](#hook-reference)
6. [API Endpoints](#api-endpoints)
6. [JavaScript API](#javascript-api)
7. [Template Variables](#template-variables)

## Configuration Variables

### Extension Configuration
```php
// extension.json
{
    "config": {
        "IslamDashboardEnableWidgetX": {
            "description": "Enable experimental Widget X",
            "value": false,
            "public": true
        },
        "IslamDashboardDefaultLayout": {
            "description": "Default dashboard layout",
            "value": "three-column",
            "public": true
        }
    }
}
```

### User Preferences
```php
// Access via User::getOption()
$user->getOption( 'islamdashboard-layout' );
$user->getOption( 'islamdashboard-widgets' );
```

## Global Variables

### PHP Globals
```php
// Access to MediaWiki services
$services = MediaWikiServices::getInstance();
$config = $services->getConfigFactory()->makeConfig( 'IslamDashboard' );
$permissionManager = $services->getPermissionManager();
```

### JavaScript Globals
```javascript
// Dashboard configuration
mw.config.get( 'wgIslamDashboardConfig' );

// Current user info
mw.user;

// API instance
new mw.Api();
```

## Core Classes

### Main Classes
- `MediaWiki\Extension\IslamDashboard\IslamDashboard` - Main extension class
- `MediaWiki\Extension\IslamDashboard\SpecialIslamDashboard` - Special page implementation
- `MediaWiki\Extension\IslamDashboard\WidgetFactory` - Widget management

### Widget Base
```php
namespace MediaWiki\Extension\IslamDashboard\Widgets;

abstract class Widget {
    abstract public function getName(): string;
    abstract public function getContent(): string;
    public function getRequiredRights(): array { return []; }
    public function getJSModule(): ?string { return null; }
    public function getCSSModule(): ?array { return []; }
}
```

## Hook Reference

### Hooks Available
- `IslamDashboardRegisterWidgets` - Register new widget classes
- `IslamDashboardBeforeDisplay` - Modify dashboard output
- `IslamDashboardGetWidgets` - Filter available widgets
- `IslamDashboardMakeGlobalVariablesScript` - Add JS globals

### Example Usage
```php
$wgHooks['IslamDashboardRegisterWidgets'][] = function( &$widgets ) {
    $widgets[] = 'MyCustomWidget';
    return true;
};
```

## API Endpoints

### Core Endpoints
- `/api.php?action=islamdashboard-get-widgets`
- `/api.php?action=islamdashboard-save-layout`
- `/api.php?action=islamdashboard-get-data&widget=widgetName`

### Example Request
```javascript
new mw.Api().get({
    action: 'islamdashboard-get-data',
    widget: 'recent-changes',
    format: 'json'
}).done( function( data ) {
    // Handle response
} );
```

## JavaScript API

### Core Modules
- `ext.islamDashboard.core` - Core functionality
- `ext.islamDashboard.widgets` - Widget management
- `ext.islamDashboard.ui` - UI components

### Events
```javascript
// Widget initialized
$( document ).on( 'islamdashboard-widget-initialized', function( e, widgetId ) {
    // Handle widget init
} );

// Dashboard ready
mw.hook( 'islamdashboard.ready' ).add( function() {
    // Dashboard is ready
} );
```

## Template Variables

### Available in All Templates
- `$dashboardTitle` - Current dashboard title
- `$user` - Current user object
- `$skin` - Current skin object
- `$widgets` - Array of available widgets

### Widget Template
```html
<div class="islamdashboard-widget" id="widget-<?php echo htmlspecialchars( $widget->getName() ); ?>">
    <h3 class="widget-title"><?php echo htmlspecialchars( $widget->getTitle() ); ?></h3>
    <div class="widget-content">
        <?php echo $widget->getContent(); ?>
    </div>
</div>
```

## IslamSkin Configuration

### Theme Settings
```php
// In LocalSettings.php
$wgIslamThemeDefault = 'auto'; // 'auto', 'light', or 'dark'
$wgIslamEnableDarkMode = true; // Enable/disable dark mode
```

### Layout Options
```php
$wgIslamStickyHeader = true; // Enable sticky header
$wgIslamShowPageTools = true; // Show page tools menu
$wgIslamShowSiteStats = true; // Show site statistics in footer
```

### Navigation
```php
$wgIslamMainMenuItems = [
    'mainpage' => [
        'text' => 'Main Page',
        'href' => '/wiki/Main_Page',
        'icon' => 'home'
    ],
    // Add more menu items as needed
];
```

### Resource Modules
IslamSkin provides several built-in modules:
- `skins.islam.styles`: Core styles
- `skins.islam.scripts`: Core JavaScript
- `skins.islam.search`: Search functionality
- `skins.islam.commandPalette`: Command palette feature
- `skins.islam.preferences`: User preferences UI

### Template Variables
Available in skin templates:
```php
$data['is-dark-theme'] // Boolean for dark theme
$data['is-anon'] // Boolean for anonymous users
$data['user-info'] // Current user information
$data['main-menu'] // Main navigation menu
$data['search-box'] // Search box configuration
$data['page-tools'] // Page-specific tools
$data['footer-links'] // Footer links and information
```

### JavaScript API
```javascript
// Check if dark mode is active
mw.config.get('wgIsDarkTheme');

// Access user preferences
mw.user.options.get('islam-theme');

// Events
mw.hook('islam.skin.ready').add(function() {
    // Skin is ready
});
```

### CSS Variables
IslamSkin provides CSS variables for theming:
```css
:root {
    --color-primary: #0645ad;
    --color-secondary: #54595d;
    --color-surface: #ffffff;
    --color-text: #202122;
    /* Dark mode variables */
    --color-primary--dark: #36c;
    --color-surface--dark: #1e1e1e;
    --color-text--dark: #e0e0e0;
}
```

## Version History
- **0.3.1**: Initial version