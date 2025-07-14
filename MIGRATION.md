# Migration Guide: Upgrading to IslamDashboard 0.3.1 with IslamCore

## Overview

This guide provides step-by-step instructions for upgrading from IslamDashboard 0.3.0 (or earlier) to version 0.3.1, which introduces the new IslamCore dependency and architectural changes.

## ⚠️ Breaking Changes

1. **New Required Dependency**: IslamCore is now a required dependency
2. **Configuration Changes**: Some configuration options have moved to IslamCore
3. **API Changes**: Some internal APIs have been updated to use IslamCore services

## 🚀 Upgrade Instructions

### 1. Backup Your Installation

Before proceeding, make sure to:

1. Backup your database
2. Backup your LocalSettings.php
3. Backup any custom widgets or templates

### 2. Install IslamCore

First, install the IslamCore extension:

```bash
cd /path/to/mediawiki/extensions
git clone https://github.com/muslim-wiki/IslamCore.git
```

### 3. Update IslamDashboard

Update or reinstall IslamDashboard:

```bash
cd /path/to/mediawiki/extensions
# If you have an existing installation
cd IslamDashboard
git pull origin main

# Or for a fresh install
git clone https://github.com/muslim-wiki/IslamDashboard.git
```

### 4. Update LocalSettings.php

Update your LocalSettings.php to load IslamCore before IslamDashboard:

```php
// Load IslamCore first
wfLoadExtension( 'IslamCore' );

// Then load IslamDashboard
wfLoadExtension( 'IslamDashboard' );
```

### 5. Run Database Updates

Run the update script to apply any database changes:

```bash
php maintenance/update.php
```

## 🔧 Configuration Changes

### Moved to IslamCore

The following configurations have been moved to IslamCore. Update your LocalSettings.php accordingly:

| Old Setting | New Setting | Notes |
|-------------|-------------|-------|
| `$wgIslamDashboardWidgets` | `$wgIslamCoreWidgets` | Widget registration now handled by IslamCore |
| `$wgIslamDashboardPermissions` | `$wgIslamCorePermissions` | Permissions management moved to IslamCore |
| `$wgIslamDashboardAPISettings` | `$wgIslamCoreAPISettings` | API settings consolidated in IslamCore |

### New Configuration Options

New configuration options available in IslamCore:

```php
// Enable/disable features
$wgIslamCoreFeatures = [
    'dashboard' => true,
    'security' => true,
    'analytics' => false,
];

// Widget configuration
$wgIslamCoreWidgets = [
    'recent-changes' => [
        'class' => 'MediaWiki\\Extension\\IslamDashboard\\Widgets\\RecentChangesWidget',
        'enabled' => true,
    ],
    // ... other widgets
];
```

## 🐛 Troubleshooting

### Common Issues

1. **IslamCore Not Found**
   - Ensure IslamCore is installed and loaded before IslamDashboard
   - Verify the directory name is exactly `IslamCore`

2. **Permission Errors**
   - Check that the web server has read access to both extensions
   - Verify file permissions in the `extensions/` directory

3. **Widgets Not Appearing**
   - Check the browser console for JavaScript errors
   - Verify widgets are properly registered in `extension.json`
   - Check the web server error log for PHP errors

## 📦 For Extension Developers

If you've developed custom widgets or extensions that integrate with IslamDashboard, you'll need to update them to work with the new architecture.

### Updating Widgets

1. Update your widget class to extend the new base class:

```php
use MediaWiki\Extension\IslamCore\Widget\BaseWidget;

class MyWidget extends BaseWidget {
    // Your widget implementation
}
```

2. Update your extension's `extension.json` to require IslamCore:

```json
{
    "requires": {
        "MediaWiki": ">= 1.43.0",
        "IslamCore": ">=1.0.0"
    }
}
```

### New Hooks

IslamCore introduces new hooks you can use:

- `IslamCoreRegisterWidgets`: Register new widgets
- `IslamCoreBeforeDisplay`: Modify output before display
- `IslamCoreAfterDisplay`: Modify output after display

## 🔄 Rolling Back

If you need to roll back to a previous version:

1. Comment out the IslamDashboard and IslamCore lines in LocalSettings.php
2. Restore your previous version of IslamDashboard
3. Remove the IslamCore directory
4. Uncomment the old IslamDashboard line in LocalSettings.php
5. Run `php maintenance/update.php --quick`

## 📝 Changelog

### 0.3.1
- Added IslamCore as a required dependency
- Moved shared services to IslamCore
- Updated widget system to use IslamCore's widget manager
- Improved security with IslamCore's security model
- Updated documentation and examples

### 0.3.0
- Initial public release

## 📞 Support

For additional help, please open an issue on [GitHub](https://github.com/muslim-wiki/IslamDashboard/issues) or visit our [community forum](https://community.muslim.wiki).
