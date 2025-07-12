# Configuration Guide - IslamDashboard

## Table of Contents
- [Overview](#overview)
- [Installation](#installation)
- [Global Configuration](#global-configuration)
- [Widget Configuration](#widget-configuration)
- [Navigation Configuration](#navigation-configuration)
- [Permissions](#permissions)
- [Theming](#theming)
- [Caching](#caching)
- [Performance Tuning](#performance-tuning)
- [Troubleshooting](#troubleshooting)
- [Best Practices](#best-practices)

## Overview

This document provides comprehensive guidance on configuring the IslamDashboard extension. The dashboard is highly customizable through MediaWiki's configuration system, allowing fine-grained control over appearance, behavior, and functionality.

## Installation

### Basic Installation

1. Download and extract the extension to your `extensions/` directory
2. Add the following to your `LocalSettings.php`:

```php
// Load the extension
wfLoadExtension( 'IslamDashboard' );

// Basic configuration (optional)
$wgIslamDashboardConfig = [
    // Global settings
    'enableQuickActions' => true,
    'defaultLayout' => 'grid',
    'showInUserMenu' => true,
    
    // Widget settings
    'widgets' => [
        'welcome' => [
            'enabled' => true,
            'position' => 'top',
            'settings' => [
                'showGreeting' => true,
                'showStats' => true,
                'showEditCount' => true,
                'showRegistrationDate' => true
            ]
        ],
        'recent-activity' => [
            'enabled' => true,
            'position' => 'left',
            'settings' => [
                'limit' => 10,
                'showUser' => true,
                'showTimestamp' => true,
                'showDiff' => true
            ]
        ]
    ],
    
    // Navigation settings
    'navigation' => [
        'enabled' => true,
        'collapsible' => true,
        'defaultState' => 'expanded',
        'sticky' => true,
        'showIcons' => true
    ],
    
    // Performance settings
    'performance' => [
        'enableCaching' => true,
        'cacheExpiry' => 3600, // 1 hour
        'lazyLoadWidgets' => true,
        'minifyAssets' => true
    ],
    
    // Security settings
    'security' => [
        'requireCSRF' => true,
        'rateLimit' => [
            'enabled' => true,
            'requests' => 60,
            'per' => 60 // seconds
        ]
    ]
];
```

### Advanced Installation

For larger installations or specific requirements, consider these additional configurations:

```php
// Enable debug mode (not recommended for production)
$wgDebugToolbar = false;
$wgShowDBErrorBacktrace = false;

// Configure database table for widget persistence
$wgDBprefix = 'mw_';
$wgIslamDashboardConfig['database'] = [
    'table' => $wgDBprefix . 'user_dashboard',
    'tableFields' => [
        'user_id' => 'INT UNSIGNED NOT NULL',
        'layout' => 'TEXT',
        'widgets' => 'MEDIUMTEXT',
        'created_at' => 'BINARY(14)',
        'updated_at' => 'BINARY(14)',
        'PRIMARY KEY' => 'user_id'
    ]
];

// Configure Redis caching if available
if ( class_exists( 'Redis' ) ) {
    $wgObjectCaches['redis'] = [
        'class' => 'RedisBagOStuff',
        'servers' => [ '127.0.0.1:6379' ],
        'persistent' => true,
    ];
    $wgMainCacheType = 'redis';
    $wgSessionCacheType = 'redis';
    $wgIslamDashboardConfig['cacheType'] = 'redis';
}
```

## Global Configuration

### Basic Settings

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `enableQuickActions` | bool | `true` | Enable quick action buttons in the dashboard header |
| `defaultLayout` | string | `'grid'` | Default layout mode (`'grid'` or `'list'`) |
| `showInUserMenu` | bool | `true` | Show dashboard link in user menu |
| `enableResponsive` | bool | `true` | Enable responsive design for mobile devices |
| `defaultDashboard` | string | `'home'` | Default dashboard to show on first visit |
| `enablePersonalization` | bool | `true` | Allow users to customize their dashboard |
| `maxColumns` | int | `3` | Maximum number of columns in grid layout |
| `enableFullscreen` | bool | `true` | Allow fullscreen mode |
| `enablePrint` | bool | `true` | Enable print styles and functionality |

### Performance Settings

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `enableCaching` | bool | `true` | Enable widget output caching |
| `cacheExpiry` | int | `3600` | Cache TTL in seconds |
| `lazyLoadWidgets` | bool | `true` | Load widget content asynchronously |
| `minifyAssets` | bool | `true` | Minify CSS and JavaScript assets |
| `combineAssets` | bool | `true` | Combine CSS and JavaScript files |
| `enableGzip` | bool | `true` | Enable GZIP compression for assets |

### Security Settings

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `requireCSRF` | bool | `true` | Require CSRF tokens for dashboard actions |
| `rateLimit.enabled` | bool | `true` | Enable rate limiting for API requests |
| `rateLimit.requests` | int | `60` | Number of requests allowed per period |
| `rateLimit.per` | int | `60` | Rate limit period in seconds |
| `allowedHtmlTags` | array | `['b', 'i', 'u', 'em', 'strong', 'a', 'ul', 'ol', 'li', 'p', 'br', 'code']` | Allowed HTML tags in widget content |
| `enableXssProtection` | bool | `true` | Enable XSS protection headers |

## Widget Configuration

### Available Widgets

#### Welcome Widget

```php
'welcome' => [
    'enabled' => true,
    'position' => 'top',
    'settings' => [
        'showGreeting' => true,
        'showStats' => true,
        'showEditCount' => true,
        'showRegistrationDate' => true,
        'showLastActive' => true,
        'showAvatar' => true,
        'avatarSize' => 'medium', // small, medium, large
        'greetingText' => 'Welcome back, {name}!',
        'customCssClass' => ''
    ]
]
```

#### Recent Activity Widget

```php
'recent-activity' => [
    'enabled' => true,
    'position' => 'left',
    'settings' => [
        'limit' => 10,
        'showUser' => true,
        'showTimestamp' => true,
        'showDiff' => true,
        'showPageLink' => true,
        'showUserLink' => true,
        'showEditSummary' => true,
        'showBotEdits' => false,
        'showMinorEdits' => true,
        'groupByPage' => false,
        'refreshInterval' => 300 // seconds
    ]
]
```

#### Quick Actions Widget

```php
'quick-actions' => [
    'enabled' => true,
    'position' => 'right',
    'settings' => [
        'actions' => [
            'edit' => [
                'label' => 'Edit this page',
                'icon' => 'pencil',
                'permission' => 'edit',
                'action' => 'edit',
                'class' => 'action-edit'
            ],
            'history' => [
                'label' => 'Page history',
                'icon' => 'history',
                'permission' => 'read',
                'action' => 'history',
                'class' => 'action-history'
            ]
        ],
        'showLabels' => true,
        'showIcons' => true,
        'layout' => 'grid' // grid or list
    ]
]
```

### Custom Widgets

To add a custom widget:

1. Create a new widget class in `extensions/IslamDashboard/includes/Widgets/`
2. Register it in `extension.json`
3. Add configuration to `$wgIslamDashboardConfig['widgets']`

Example custom widget registration:

```php
// In LocalSettings.php
$wgIslamDashboardConfig['widgets']['my-custom-widget'] = [
    'enabled' => true,
    'position' => 'left',
    'settings' => [
        'title' => 'My Custom Widget',
        'content' => 'This is a custom widget',
        'refreshInterval' => 0 // Disable auto-refresh
    ]
];
```

## Navigation Configuration

### Basic Navigation

```php
'navigation' => [
    'enabled' => true,
    'collapsible' => true,
    'defaultState' => 'expanded', // or 'collapsed'
    'sticky' => true,
    'showIcons' => true,
    'showBadges' => true,
    'maxDepth' => 2,
    'sections' => [
        'main' => [
            'label' => 'Main',
            'icon' => 'home',
            'items' => [
                'dashboard' => [
                    'label' => 'Dashboard',
                    'icon' => 'tachometer-alt',
                    'href' => '/wiki/Special:Dashboard',
                    'permission' => 'read',
                    'order' => 10
                ]
            ]
        ]
    ]
]
```

### Dynamic Navigation Items

You can add navigation items dynamically using hooks:

```php
$wgHooks['IslamDashboardNavigationInit'][] = function ( $navigationManager ) {
    $section = $navigationManager->getSection( 'main' );
    if ( $section ) {
        $section->addItem( new \MediaWiki\Extension\IslamDashboard\Navigation\NavigationItem(
            'custom-item',
            'Custom Item',
            'star',
            '/wiki/Custom_Page',
            'read',
            100
        ) );
    }
    return true;
};
```

## Permissions

### Built-in Permissions

| Permission | Description | Default Groups |
|------------|-------------|----------------|
| `view-dashboard` | Access the dashboard | `['user', 'autoconfirmed', 'editor', 'sysop']` |
| `edit-dashboard` | Customize dashboard layout | `['user', 'autoconfirmed', 'editor', 'sysop']` |
| `manage-dashboard` | Manage dashboard settings | `['sysop', 'bureaucrat']` |
| `view-widget-{widget}` | View specific widget | Inherits from `view-dashboard` |
| `configure-widget-{widget}` | Configure specific widget | Inherits from `edit-dashboard` |

### Customizing Permissions

```php
// In LocalSettings.php
$wgGroupPermissions['*']['view-dashboard'] = true; // Allow anonymous access
$wgGroupPermissions['user']['edit-dashboard'] = true;
$wgGroupPermissions['sysop']['manage-dashboard'] = true;

// Custom widget permissions
$wgAvailableRights[] = 'view-widget-my-widget';
$wgGroupPermissions['user']['view-widget-my-widget'] = true;
```

## Theming

### CSS Variables

Customize the dashboard appearance by overriding these CSS variables in your `MediaWiki:Common.css` or skin:

```css
:root {
    /* Colors */
    --islam-dashboard-primary: #36c;
    --islam-dashboard-secondary: #54595d;
    --islam-dashboard-success: #00af89;
    --islam-dashboard-warning: #fc3;
    --islam-dashboard-danger: #d33;
    --islam-dashboard-light: #f8f9fa;
    --islam-dashboard-dark: #202122;
    
    /* Typography */
    --islam-dashboard-font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    --islam-dashboard-font-size-base: 14px;
    --islam-dashboard-line-height-base: 1.5;
    
    /* Spacing */
    --islam-dashboard-spacing-unit: 1rem;
    --islam-dashboard-border-radius: 0.25rem;
    --islam-dashboard-box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    
    /* Widgets */
    --islam-dashboard-widget-bg: #fff;
    --islam-dashboard-widget-border: 1px solid #eaecf0;
    --islam-dashboard-widget-header-bg: #f8f9fa;
    --islam-dashboard-widget-header-color: #202122;
    
    /* Navigation */
    --islam-dashboard-nav-bg: #f8f9fa;
    --islam-dashboard-nav-color: #202122;
    --islam-dashboard-nav-hover-bg: #eaecf0;
    --islam-dashboard-nav-active-bg: #eaf3ff;
    --islam-dashboard-nav-active-color: #36c;
    --islam-dashboard-nav-width: 250px;
    --islam-dashboard-nav-collapsed-width: 60px;
}
```

### Custom Styles

Add custom CSS in `MediaWiki:IslamDashboard.css` or your skin's stylesheet:

```css
/* Custom widget styles */
.widget-my-custom-widget {
    border-left: 3px solid var(--islam-dashboard-primary);
}

/* Custom navigation styles */
.ext-islamdashboard-navigation .nav-item.active {
    font-weight: bold;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .ext-islamdashboard-dashboard {
        padding: 0.5rem;
    }
}
```

## Caching

### Configuration

```php
// In LocalSettings.php
$wgIslamDashboardConfig['caching'] = [
    'enabled' => true,
    'type' => 'apcu', // apcu, memcached, redis, or wincache
    'prefix' => 'islamdashboard_',
    'ttl' => 3600, // 1 hour
    'stale' => 300, // 5 minutes (serve stale content while refreshing)
    'version' => '1.0.0' // Bump to invalidate all caches
];

// Example with Redis
if ( class_exists( 'Redis' ) ) {
    $wgObjectCaches['redis'] = [
        'class' => 'RedisBagOStuff',
        'servers' => [ '127.0.0.1:6379' ],
        'persistent' => true,
    ];
    $wgIslamDashboardConfig['caching']['type'] = 'redis';
}
```

### Cache Invalidation

```php
// In your extension code
$cache = \MediaWiki\MediaWikiServices::getInstance()->getMainWANObjectCache();
$cacheKey = $cache->makeKey( 'islamdashboard', 'widget', $widgetId, $userId );

// Clear specific widget cache
$cache->delete( $cacheKey );

// Clear all dashboard caches for a user
$cache->delete( $cache->makeKey( 'islamdashboard', 'user', $userId ) );

// Clear all dashboard caches
$cache->delete( $cache->makeKey( 'islamdashboard', 'all' ) );
```

## Performance Tuning

### Database Optimization

```sql
-- Add indexes for better performance
ALTER TABLE /*_*/user_dashboard 
    ADD INDEX user_id_idx (user_id),
    ADD INDEX updated_at_idx (updated_at);

-- Optimize table
OPTIMIZE TABLE /*_*/user_dashboard;
```

### PHP OPcache

Enable and configure PHP OPcache in your `php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.validate_timestamps=1
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### Web Server Configuration

#### Nginx

```nginx
# Enable gzip compression
gzip on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
gzip_comp_level 6;
gzip_min_length 1000;
gzip_proxied any;

# Cache static assets
location ~* \.(?:css|js|woff2?|ttf|eot|svg|png|jpe?g|gif|ico)$ {
    expires 30d;
    add_header Cache-Control "public";
    access_log off;
}
```

#### Apache

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType text/x-javascript "access plus 1 month"
    ExpiresByType application/x-shockwave-flash "access plus 1 month"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresDefault "access plus 2 days"
</IfModule>
```

## Troubleshooting

### Common Issues

#### Widgets Not Loading

1. Check browser console for JavaScript errors
2. Verify widget permissions
3. Check server error logs
4. Disable browser extensions that might interfere
5. Try in incognito/private browsing mode

#### Permission Issues

1. Verify user has required permissions:
   ```php
   // Check user permissions
   $user = \RequestContext::getMain()->getUser();
   if ( $user->isAllowed( 'view-dashboard' ) ) {
       // User can view dashboard
   }
   ```

2. Check group permissions in `LocalSettings.php`
3. Verify user group memberships

#### Styling Issues

1. Check for CSS conflicts with other extensions or skins
2. Verify custom CSS is being loaded
3. Check for missing or incorrect font files
4. Test in multiple browsers

### Debugging

Enable debug mode in `LocalSettings.php`:

```php
// Enable debug logging
$wgDebugLogFile = "$IP/logs/debug.log";
$wgDebugToolbar = true;
$wgShowExceptionDetails = true;
$wgShowDBErrorBacktrace = true;
$wgDebugLogGroups['islamdashboard'] = "$IP/logs/islamdashboard-debug.log";

// Disable caching for development
$wgMainCacheType = CACHE_NONE;
$wgParserCacheType = CACHE_NONE;
$wgMessageCacheType = CACHE_NONE;
$wgIslamDashboardConfig['caching']['enabled'] = false;
```

## Best Practices

### Configuration Management

1. **Version Control**: Track changes to `LocalSettings.php`
2. **Environment Variables**: Use environment-specific configurations
3. **Documentation**: Document all custom configurations
4. **Testing**: Test changes in a staging environment first

### Performance Optimization

1. **Enable Caching**: Use appropriate caching mechanisms
2. **Optimize Images**: Compress and resize images
3. **Minify Assets**: Minify CSS and JavaScript
4. **Use CDN**: For static assets
5. **Database Optimization**: Regular maintenance and indexing

### Security Considerations

1. **Input Validation**: Always validate and sanitize user input
2. **Output Escaping**: Escape all dynamic content
3. **CSRF Protection**: Enable CSRF protection
4. **Rate Limiting**: Implement rate limiting for API endpoints
5. **Regular Updates**: Keep the extension and dependencies updated

### Accessibility

1. **Keyboard Navigation**: Ensure all functionality is accessible via keyboard
2. **ARIA Attributes**: Use appropriate ARIA roles and attributes
3. **Color Contrast**: Maintain sufficient contrast ratios
4. **Screen Reader Support**: Test with screen readers
5. **Responsive Design**: Ensure usability on all device sizes

### Maintenance

1. **Regular Backups**: Backup dashboard configurations
2. **Monitoring**: Monitor performance and errors
3. **Cleanup**: Remove unused widgets and configurations
4. **Updates**: Keep the extension updated
5. **Documentation**: Keep documentation up to date
];
```

## Global Configuration

### `$wgIslamDashboardConfig`
Main configuration array for the IslamDashboard extension.

**Type:** `array`  
**Default:** `[]`

```php
$wgIslamDashboardConfig = [
    // Global settings
    'enableQuickActions' => true,      // Enable/disable quick actions
    'defaultLayout' => 'grid',         // Default layout: 'grid' or 'list'
    'showInUserMenu' => true,          // Show dashboard in user menu
    'enableWidgetManagement' => true,  // Allow users to manage their widgets
    'defaultWidgets' => [              // Default widgets for new users
        'welcome',
        'recent-activity',
        'quick-actions'
    ],
    // ... other settings
];
```

### `$wgIslamDashboardEnableWidgetManagement`
Enable or disable widget management for users.

**Type:** `bool`  
**Default:** `true`

```php
$wgIslamDashboardEnableWidgetManagement = true;
```

### `$wgIslamDashboardDefaultWidgets`
Define default widgets for new users.

**Type:** `array`  
**Default:** `['welcome', 'recent-activity', 'quick-actions']`

```php
$wgIslamDashboardDefaultWidgets = [
    'welcome',
    'recent-activity',
    'quick-actions'
];
```

### `$wgIslamDashboardEnableAnalytics`
Enable anonymous usage analytics.

**Type:** `bool`  
**Default:** `false`

```php
$wgIslamDashboardEnableAnalytics = false;
```

## Widget Configuration

### Widget Registration

Widgets can be registered in `extension.json` or programmatically:

```json
"Widgets": {
    "Welcome": "includes/Widgets/WelcomeWidget.php",
    "RecentActivity": "includes/Widgets/RecentActivityWidget.php"
}
```

### Widget Settings

Each widget can have its own configuration:

```php
$wgIslamDashboardConfig['widgets'] = [
    'welcome' => [
        'enabled' => true,
        'position' => 'top',
        'settings' => [
            'showGreeting' => true,
            'showStats' => true,
            'customMessage' => 'Welcome to your dashboard!'
        ]
    ],
    'recent-activity' => [
        'enabled' => true,
        'limit' => 10,
        'showUserAvatars' => true
    ]
];
```

## Navigation Configuration

### `$wgIslamDashboardNavigation`
Configure the dashboard navigation menu.

**Type:** `array`  
**Default:** `[]`

```php
$wgIslamDashboardNavigation = [
    'dashboard' => [
        'label' => 'Dashboard',
        'icon' => 'dashboard',
        'permission' => 'view-dashboard',
        'items' => [
            'home' => [
                'label' => 'Home',
                'href' => '/wiki/Dashboard',
                'icon' => 'home',
                'permission' => 'read'
            ],
            'profile' => [
                'label' => 'My Profile',
                'href' => '/wiki/User:' . $wgUser->getName(),
                'permission' => 'editmyuserpage'
            ]
        ]
    ]
];
```

## Permissions

### Available Permissions

- `view-dashboard`: View the dashboard (default: `user`)
- `manage-dashboard`: Customize dashboard layout (default: `user`)
- `edit-dashboard`: Edit dashboard content (default: `user`)
- `admin-dashboard`: Full dashboard administration (default: `sysop`)

### Configuring Permissions

```php
// In LocalSettings.php
$wgGroupPermissions['*']['view-dashboard'] = false; // Disable for anonymous users
$wgGroupPermissions['user']['view-dashboard'] = true; // Allow logged-in users
$wgGroupPermissions['sysop']['admin-dashboard'] = true; // Full admin access
```

## Theming

The dashboard supports custom theming through CSS variables. You can override these in your MediaWiki:Common.css or a custom skin.

### Available CSS Variables

```css
:root {
    /* Colors */
    --color-primary: #36c;
    --color-secondary: #54595d;
    --color-success: #00af89;
    --color-warning: #ffcc33;
    --color-danger: #d33;
    
    /* Layout */
    --sidebar-width: 250px;
    --header-height: 60px;
    
    /* Widgets */
    --widget-border-radius: 4px;
    --widget-box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
```

## Caching

The dashboard supports various caching mechanisms to improve performance.

### `$wgIslamDashboardCacheExpiry`
Default cache expiry time in seconds.

**Type:** `int`  
**Default:** `3600` (1 hour)

```php
$wgIslamDashboardCacheExpiry = 3600;
```

### Disable Caching

```php
$wgIslamDashboardCacheExpiry = 0; // Disable caching
```

## Performance Tuning

### `$wgIslamDashboardMaxWidgetsPerPage`
Maximum number of widgets to load per page.

**Type:** `int`  
**Default:** `20`

```php
$wgIslamDashboardMaxWidgetsPerPage = 20;
```

### `$wgIslamDashboardLazyLoadWidgets`
Enable lazy loading of widgets.

**Type:** `bool`  
**Default:** `true`

```php
$wgIslamDashboardLazyLoadWidgets = true;
```

## Troubleshooting

### Common Issues

1. **Widgets not loading**
   - Check browser console for JavaScript errors
   - Verify widget registration in extension.json
   - Check user permissions

2. **Layout issues**
   - Clear browser cache
   - Check for CSS conflicts with your skin
   - Verify responsive breakpoints in your CSS

3. **Performance problems**
   - Enable widget lazy loading
   - Increase cache duration
   - Reduce the number of widgets per page

### Debugging

Enable debug mode in LocalSettings.php:

```php
$wgDebugToolbar = true;
$wgShowDebug = true;
$wgShowSQLErrors = true;
$wgDevelopmentWarnings = true;
$wgDebugLogFile = '/tmp/mw-debug.log';
```

### Getting Help

For additional support:
1. Check the [GitHub issues](https://github.com/your-repo/IslamDashboard/issues)
2. Visit the [MediaWiki support forum](https://www.mediawiki.org/wiki/Manual:FAQ)
3. Contact the extension maintainers

```php
$wgIslamDashboardDefaultLayout = 'default';
```

#### `$wgIslamDashboardEnableAnalytics`
Enable analytics for the dashboard.

**Default:** `false`

```php
$wgIslamDashboardEnableAnalytics = false;
```

## Widget Configuration

### Enabling/Disabling Widgets
Control which widgets are available:

```php
$wgIslamDashboardAvailableWidgets = [
    'welcome' => true,
    'recent-activity' => true,
    'quick-actions' => true,
    // Add custom widgets here
];
```

### Widget-Specific Settings

#### Welcome Widget
```php
$wgIslamDashboardWelcomeWidget = [
    'showGreeting' => true,
    'showStats' => true,
    'showQuickLinks' => true
];
```

#### Recent Activity Widget
```php
$wgIslamDashboardRecentActivityWidget = [
    'limit' => 10,
    'showTimestamps' => true,
    'showUserAvatars' => true
];
```

## User Preferences

Users can customize their dashboard through Special:Preferences. The following preferences are available:

- `islamdashboard-layout`: Dashboard layout preference
- `islamdashboard-widgets`: Enabled/disabled widgets
- `islamdashboard-theme`: Color scheme preference

## Permissions

### Default Permissions
- `view-dashboard`: View the dashboard (included in 'read' group by default)
- `edit-dashboard`: Customize the dashboard (included in 'user' group by default)
- `manage-dashboard`: Manage dashboard settings (included in 'sysop' group by default)

### Modifying Permissions
```php
// Example: Allow only administrators to edit the dashboard
$wgGroupPermissions['user']['edit-dashboard'] = false;
$wgGroupPermissions['sysop']['edit-dashboard'] = true;
```

## Theming

### CSS Variables
Customize the dashboard appearance using CSS variables:

```css
:root {
    --dashboard-bg: #ffffff;
    --dashboard-text: #202122;
    --dashboard-primary: #36c;
    --dashboard-border: #a2a9b1;
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    :root {
        --dashboard-bg: #222;
        --dashboard-text: #eee;
        --dashboard-border: #444;
    }
}
```

### Custom Styles
Add custom CSS through MediaWiki:Common.css or your skin's stylesheet:

```css
/* Example: Custom widget styling */
.widget--my-custom-widget {
    border-left: 3px solid var(--dashboard-primary);
}
```

## Caching

### Cache Configuration
```php
// Cache dashboard data for 1 hour
$wgIslamDashboardCacheExpiry = 3600;

// Disable caching (for development)
$wgIslamDashboardCacheExpiry = 0;
```

### Clearing the Cache
```php
// Clear dashboard cache programmatically
$cache = MediaWikiServices::getInstance()->getMainWANObjectCache();
$cache->delete($cache->makeKey('islamdashboard', 'user', $userId));
```

## Troubleshooting

### Common Issues

#### Widgets Not Appearing
1. Check if the widget is enabled in `$wgIslamDashboardAvailableWidgets`
2. Verify the widget class is properly registered
3. Check browser console for JavaScript errors

#### Permission Issues
1. Verify user has the required permissions
2. Check group permissions in LocalSettings.php
3. Ensure the user is logged in (if required)

#### Styling Issues
1. Check for CSS conflicts with other extensions
2. Verify CSS variables are properly defined
3. Clear browser cache

### Debugging
Enable debug mode for more detailed error messages:

```php
$wgDebugLogFile = "$IP/debug.log";
$wgShowExceptionDetails = true;
$wgShowDBErrorBacktrace = true;
$wgShowSQLErrors = true;
```

## See Also
- [Widget Development Guide](./WIDGETS.md)
- [API Reference](./API_REFERENCE.md)
- [Architecture](./ARCHITECTURE.md)
