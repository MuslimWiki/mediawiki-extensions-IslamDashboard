# IslamDashboard Extensibility Guide

## Version: 0.3.1

## Overview
This document explains how to extend the IslamDashboard with custom functionality, widgets, and integrations.

## Extension Points

### 1. Widget System
Create custom widgets that can be added to the dashboard.

**Example**:
```php
// In your extension's extension.json
"IslamDashboardWidgets": {
    "MyCustomWidget": "MyExtension\\Widgets\\MyCustomWidget"
}
```

### 2. Hooks

#### Available Hooks

##### `IslamDashboardRegisterWidgets`
Register new widget classes.

```php
$wgHooks['IslamDashboardRegisterWidgets'][] = function( &$widgets ) {
    $widgets[] = 'MyCustomWidget';
    return true;
};
```

##### `IslamDashboardBeforeDisplay`
Modify dashboard content before display.

```php
$wgHooks['IslamDashboardBeforeDisplay'][] = function( &$output, $context ) {
    // Modify $output
    return true;
};
```

### 3. API Modules
Create custom API endpoints for dynamic content.

**Example**:
```php
class ApiMyCustomEndpoint extends ApiBase {
    public function execute() {
        $result = [ 'status' => 'success' ];
        $this->getResult()->addValue( null, 'mycustom', $result );
    }
}
```

## JavaScript Extensibility

### Events
Subscribe to dashboard events:

```javascript
// Widget initialization
mw.hook('islamdashboard.widgets').add(function(widgets) {
    widgets.register('my-widget', {
        init: function(element, config) {
            // Initialize widget
        }
    });
});

// Dashboard ready
$(document).on('islamdashboard:ready', function() {
    // Dashboard is ready
});
```

### Custom UI Components
Create reusable UI components:

```javascript
// In your ResourceLoader module
module.exports = {
    MyComponent: function(config) {
        this.render = function() {
            return '<div class="my-component">Content</div>';
        };
    }
};
```

## Best Practices

### Performance
1. Load JavaScript asynchronously
2. Use ResourceLoader modules efficiently
3. Implement client-side caching

### Security
1. Validate all inputs
2. Escape all outputs
3. Use CSRF tokens for state-changing operations

### Accessibility
1. Use semantic HTML
2. Support keyboard navigation
3. Provide ARIA attributes

## Version History
- **0.3.1**: Initial version
