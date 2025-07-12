# IslamDashboard Configuration

This document outlines the configuration options available for the IslamDashboard extension.

## Table of Contents

- [Basic Configuration](#basic-configuration)
- [Widget Configuration](#widget-configuration)
- [Permissions](#permissions)
- [Customization](#customization)
- [Troubleshooting](#troubleshooting)

## Basic Configuration

Add these options to your `LocalSettings.php` file to configure the basic behavior of the IslamDashboard extension.

```php
// Enable or disable the dashboard
$wgIslamDashboardEnable = true;

// Set the default dashboard layout (default, compact, spacious)
$wgIslamDashboardDefaultLayout = 'default';

// Show the dashboard link in the user menu
$wgIslamDashboardShowInUserMenu = true;

// Enable analytics for the dashboard
$wgIslamDashboardEnableAnalytics = false;
```

## Widget Configuration

Configure which widgets are available and their default visibility.

```php
// Enable or disable specific widgets
$wgIslamDashboardWidgets = [
    'welcome' => true,
    'recent-activity' => true,
    'quick-actions' => true,
    'notifications' => true,
    'quick-links' => true
];

// Set default widget positions
$wgIslamDashboardDefaultWidgetPositions = [
    'main' => ['welcome', 'recent-activity'],
    'sidebar' => ['quick-actions', 'notifications', 'quick-links']
];

// Maximum number of items to show in activity feeds
$wgIslamDashboardMaxActivityItems = 10;

// Enable/disable widget customization for users
$wgIslamDashboardAllowWidgetCustomization = true;
```

## Permissions

Configure who can access and modify the dashboard.

```php
// Permission required to view the dashboard
// Default: 'read' (all logged-in users)
$wgIslamDashboardViewPermission = 'read';

// Permission required to customize the dashboard
// Default: 'editmyoptions' (users with preferences edit access)
$wgIslamDashboardCustomizePermission = 'editmyoptions';

// Permission required to view all users' dashboards (for admins)
// Default: 'viewdashboard-all' (no one by default)
$wgIslamDashboardViewAllPermission = 'viewdashboard-all';
```

## Customization

### Adding Custom Widgets

You can add custom widgets to the dashboard by implementing the `IslamDashboardGetWidgets` hook:

```php
// In your extension's setup file
$wgHooks['IslamDashboardGetWidgets'][] = function ( &$widgets ) {
    $widgets[] = [
        'id' => 'my-custom-widget',
        'title' => wfMessage( 'myextension-custom-widget-title' )->text(),
        'description' => wfMessage( 'myextension-custom-widget-desc' )->text(),
        'callback' => 'MyExtension::renderCustomWidget',
        'defaultSection' => 'main',
        'canHide' => true
    ];
    return true;
};
```

### Theming

You can customize the dashboard's appearance by adding CSS to your wiki's `MediaWiki:Common.css` or your skin's stylesheet:

```css
/* Example: Change the primary color */
.islam-dashboard-container {
    --color-primary: #36c;
    --color-primary--hover: #2a4b8d;
}

/* Example: Custom widget styling */
.dashboard-widget.my-custom-widget {
    border-left: 3px solid var( --color-primary );
}
```

## Troubleshooting

### Dashboard Not Appearing

1. Verify the extension is properly installed and enabled in `LocalSettings.php`:
   ```php
   wfLoadExtension( 'IslamDashboard' );
   ```

2. Check that the user has the required permissions to view the dashboard.

3. Ensure the Islam Skin or a compatible skin is installed and active.

### Widgets Not Loading

1. Check the browser's developer console for JavaScript errors.
2. Verify that the widget files exist and are properly registered.
3. Check the wiki's error log for any PHP errors.

### Custom Widget Issues

1. Ensure your widget's callback function is properly defined and accessible.
2. Check that your widget returns valid HTML.
3. Verify that any required resources (CSS, JS) are loaded.

## Support

For additional help, please refer to:
- [Extension documentation](https://www.mediawiki.org/wiki/Extension:IslamDashboard)
- [Support forum](https://www.mediawiki.org/wiki/Extension_talk:IslamDashboard)
- [Issue tracker](https://github.com/muslim-wiki/IslamDashboard/issues)
