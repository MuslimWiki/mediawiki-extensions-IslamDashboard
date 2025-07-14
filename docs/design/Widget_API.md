# IslamDashboard Widget API

## Version: 0.3.1

## Overview
This document outlines the API for creating and managing widgets in the IslamDashboard. Widgets are modular components that can be added to the dashboard to display information or provide functionality.

## Widget Structure

### Basic Widget Class
```php
namespace MediaWiki\Extension\IslamDashboard\Widgets;

use MediaWiki\Extension\IslamDashboard\IWidget;

class ExampleWidget implements IWidget {
    public static function getName(): string {
        return 'example-widget';
    }
    
    public function getTitle(): string {
        return wfMessage('islamdashboard-examplewidget-title')->text();
    }
    
    public function getContent(): string {
        // Return widget HTML content
        return '<div class="example-widget">Widget Content</div>';
    }
    
    public function getRequiredRights(): array {
        return []; // Any required user rights
    }
    
    public function getJSModule(): ?string {
        return 'ext.islamDashboard.exampleWidget';
    }
    
    public function getCSSModule(): ?array {
        return [ 'ext.islamDashboard.exampleWidget.styles' ];
    }
}
```

## Widget Registration

### Extension Registration (extension.json)
```json
"IslamDashboardWidgets": {
    "ExampleWidget": "MediaWiki\\Extension\\IslamDashboard\\Widgets\\ExampleWidget"
}
```

## Widget Hooks

### Widget Registration
```php
$wgHooks['IslamDashboardRegisterWidgets'][] = function( &$widgets ) {
    $widgets[] = 'ExampleWidget';
    return true;
};
```

### Widget Data Loading
```php
$wgHooks['IslamDashboardLoadWidgetData'][] = function( $widgetName, &$data ) {
    if ( $widgetName === 'ExampleWidget' ) {
        $data['customData'] = 'value';
    }
    return true;
};
```

## JavaScript API

### Widget Initialization
```javascript
mw.hook('islamdashboard.widgets').add(function(widgets) {
    widgets.register('example-widget', {
        init: function(element, config) {
            // Initialize widget
        },
        update: function(data) {
            // Handle data updates
        }
    });
});
```

## Best Practices
1. **Performance**: Load data asynchronously
2. **Security**: Sanitize all output
3. **Accessibility**: Follow WCAG guidelines
4. **Responsive**: Work on all screen sizes
5. **Caching**: Implement proper caching strategies

## Version History
- **0.3.1**: Initial version
