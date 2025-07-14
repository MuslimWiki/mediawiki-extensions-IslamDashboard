# IslamDashboard - IslamCore Integration Plan

## Overview
This document outlines the steps required to update the IslamDashboard extension to work with the new IslamCore architecture. The goal is to leverage the shared services and infrastructure provided by IslamCore while maintaining all existing functionality.

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Architecture Changes](#architecture-changes)
3. [Code Migration](#code-migration)
4. [Configuration Updates](#configuration-updates)
5. [Testing Strategy](#testing-strategy)
6. [Deployment Plan](#deployment-plan)
7. [Rollback Strategy](#rollback-strategy)
8. [Post-Migration Tasks](#post-migration-tasks)

## Prerequisites

- [ ] IslamCore extension installed and configured
- [ ] Backup of current IslamDashboard installation
- [ ] Development environment with MediaWiki 1.43+
- [ ] Composer for dependency management

## Architecture Changes

### Current Architecture
```
IslamDashboard/
├── includes/
│   └── Hooks.php
├── modules/
├── resources/
└── specials/
```

### New Architecture with IslamCore
```
IslamDashboard/
├── includes/
│   └── Hooks.php
├── resources/
├── src/
│   ├── Special/
│   │   └── IslamDashboard.php
│   └── Widgets/
│       └── DashboardWidget.php
└── tests/
```

## Code Migration

### 1. Update extension.json

```json
{
    "name": "IslamDashboard",
    "version": "2.0.0",
    "author": ["Muslim.Wiki Team"],
    "url": "https://muslim.wiki/",
    "descriptionmsg": "islamboard-desc",
    "license-name": "GPL-3.0-or-later",
    "type": "other",
    "requires": {
        "MediaWiki": ">= 1.43.0",
        "IslamCore": ">=1.0.0"
    },
    "AutoloadClasses": {
        "MediaWiki\\Extension\\IslamDashboard\\Hooks": "includes/Hooks.php",
        "MediaWiki\\Extension\\IslamDashboard\\SpecialIslamDashboard": "src/Special/IslamDashboard.php"
    },
    "Hooks": {
        "IslamCoreRegisterWidgets": "MediaWiki\\Extension\\IslamDashboard\\Hooks::onRegisterWidgets"
    },
    "SpecialPages": {
        "IslamDashboard": "MediaWiki\\Extension\\IslamDashboard\\SpecialIslamDashboard"
    },
    "MessagesDirs": {
        "IslamDashboard": ["i18n"]
    },
    "ResourceModules": {
        "ext.islamdashboard.styles": {
            "styles": ["resources/dashboard.less"],
            "targets": ["desktop", "mobile"]
        },
        "ext.islamdashboard.scripts": {
            "scripts": ["resources/dashboard.js"],
            "dependencies": ["mediawiki.api", "mediawiki.jqueryMsg"],
            "targets": ["desktop", "mobile"]
        }
    },
    "manifest_version": 2
}
```

### 2. Update Hooks

```php
// includes/Hooks.php
namespace MediaWiki\Extension\IslamDashboard;

use MediaWiki\MediaWikiServices;

class Hooks {
    public static function onRegisterWidgets( &$widgets ) {
        $widgets['dashboard-main'] = [
            'class' => 'MediaWiki\\Extension\\IslamDashboard\\Widgets\\DashboardWidget',
            'services' => [ 'IslamCore.Logger' ]
        ];
        
        return true;
    }
}
```

### 3. Create Widget Class

```php
// src/Widgets/DashboardWidget.php
namespace MediaWiki\Extension\IslamDashboard\Widgets;

use IslamCore\Widget\AbstractWidget;
use MediaWiki\MediaWikiServices;

class DashboardWidget extends AbstractWidget {
    public function getTitle() {
        return $this->msg( 'islamdashboard-dashboard-title' );
    }
    
    public function getContent() {
        $user = $this->getUser();
        
        return $this->getView( 'dashboard', [
            'welcomeMessage' => $this->msg( 'islamdashboard-welcome', $user->getName() )->text(),
            'stats' => $this->getUserStats( $user )
        ]);
    }
    
    protected function getUserStats( $user ) {
        // Fetch user statistics
        return [
            'contributions' => 42, // Example data
            'achievements' => 7,
            'tasks' => 3
        ];
    }
}
```

## Configuration Updates

### 1. Move to IslamCore Config

Move shared configuration to IslamCore's configuration system:

```php
// In your extension setup
$config = MediaWikiServices::getInstance()->getService( 'IslamCore.Config' );
$config->set( 'dashboard.defaultLayout', 'grid' );
```

### 2. Update LocalSettings.php

Update your LocalSettings.php to load IslamCore first:

```php
// Load core first
wfLoadExtension( 'IslamCore' );

// Then load other extensions
wfLoadExtension( 'IslamDashboard' );
```

## Testing Strategy

### 1. Unit Tests

Update test classes to use IslamCore's testing utilities:

```php
class DashboardTest extends \MediaWikiIntegrationTestCase {
    protected function setUp(): void {
        parent::setUp();
        $this->setMwGlobals( 'wgIslamCoreFeatures', ['dashboard' => true] );
    }
    
    public function testDashboardWidget() {
        $widget = new DashboardWidget();
        $this->assertStringContainsString(
            'Welcome',
            $widget->getContent()
        );
    }
}
```

### 2. Integration Tests

Test the integration with IslamCore:

```php
class DashboardIntegrationTest extends \MediaWikiIntegrationTestCase {
    public function testWidgetRegistration() {
        $widgets = [];
        Hooks::onRegisterWidgets( $widgets );
        
        $this->assertArrayHasKey( 'dashboard-main', $widgets );
        $this->assertInstanceOf(
            'MediaWiki\\Extension\\IslamDashboard\\Widgets\\DashboardWidget',
            new $widgets['dashboard-main']['class']()
        );
    }
}
```

## Deployment Plan

### 1. Staging Deployment

1. Deploy IslamCore to staging
2. Deploy updated IslamDashboard
3. Run database updates
4. Test all functionality
5. Verify error logs

### 2. Production Deployment

1. Schedule maintenance window
2. Backup database
3. Deploy IslamCore
4. Deploy updated IslamDashboard
5. Run database updates
6. Verify functionality
7. Re-enable site

## Rollback Strategy

### If Issues Occur

1. Revert to previous version of IslamDashboard
2. If needed, restore database backup
3. Clear caches
4. Verify rollback success

## Post-Migration Tasks

1. Update documentation
2. Monitor error logs
3. Gather user feedback
4. Plan next iteration of improvements

## Timeline

1. **Week 1**: Setup and planning
   - Set up development environment
   - Review current codebase
   - Finalize architecture

2. **Week 2-3**: Implementation
   - Update extension structure
   - Migrate widgets
   - Update services

3. **Week 4**: Testing
   - Write unit tests
   - Perform integration testing
   - Fix issues

4. **Week 5**: Deployment
   - Deploy to staging
   - User acceptance testing
   - Production deployment

## Resources

- [IslamCore Documentation](docs/design/)
- [Extension Development Guide](docs/design/Extension_Development_Guide.md)
- [API Documentation](docs/design/API_Documentation.md)
