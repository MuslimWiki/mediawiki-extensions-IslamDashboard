# API Reference - IslamDashboard

## Table of Contents
- [Overview](#overview)
- [JavaScript API](#javascript-api)
  - [Core](#javascript-core)
  - [Widgets](#javascript-widgets)
  - [Navigation](#javascript-navigation)
  - [Events](#javascript-events)
- [PHP API](#php-api)
  - [Widgets](#php-widgets)
  - [Navigation](#php-navigation)
  - [Hooks](#hooks)
- [Template System](#template-system)
- [Examples](#examples)
- [Best Practices](#best-practices)

## Overview

The IslamDashboard provides a comprehensive API for extending and customizing the dashboard experience. This document covers the available APIs for both PHP and JavaScript, including widget system, navigation, and template system.

## JavaScript API

### Core

#### `mw.islamDashboard`
Main namespace for dashboard JavaScript functionality.

##### Methods

###### `init(options)`
Initialize the dashboard with the given options.

**Parameters:**
- `options` (Object): Configuration options
  - `container` (String|jQuery): Selector or jQuery object for the dashboard container
  - `layout` (String): Initial layout mode ('grid' or 'list')
  - `widgets` (Array): Array of widget configurations
  - `apiUrl` (String): Base URL for API endpoints (default: '/w/api.php')
  - `csrfToken` (String): CSRF token for API requests
  - `currentUser` (Object): Current user information

**Example:**
```javascript
mw.islamDashboard.init({
    container: '#dashboard-container',
    layout: 'grid',
    apiUrl: mw.util.wikiScript('api'),
    csrfToken: mw.user.tokens.get('csrfToken'),
    currentUser: {
        id: mw.user.getId(),
        name: mw.user.getName(),
        groups: mw.config.get('wgUserGroups', [])
    },
    widgets: [
        { id: 'welcome', type: 'welcome', column: 0, order: 0 },
        { id: 'recent-activity', type: 'recent-activity', column: 1, order: 0 }
    ]
});
```

### Widgets

#### `mw.islamDashboard.WidgetManager`
Manages widget lifecycle and interactions.

##### Methods

###### `registerWidget(type, definition)`
Register a new widget type.

**Parameters:**
- `type` (String): Widget type identifier (must be unique)
- `definition` (Object): Widget definition
  - `template` (String|Function): HTML template or function that returns template
  - `init` (Function): Initialization function
  - `refresh` (Function): Refresh function
  - `defaults` (Object): Default widget settings
  - `styles` (Array|String): CSS/Less files to load
  - `scripts` (Array|String): JavaScript files to load
  - `i18n` (Object|Boolean): Internationalization messages

**Example:**
```javascript
// Register a simple widget
mw.islamDashboard.WidgetManager.registerWidget('my-widget', {
    template: '<div class="my-widget">{{message}}</div>',
    defaults: {
        message: 'Hello, World!',
        color: '#3366cc'
    },
    init: function($container, config) {
        // Initialize widget
        console.log('Widget initialized with config:', config);
        
        // Handle widget events
        $container.on('click', '.my-action', function() {
            alert('Action clicked!');
        });
    },
    refresh: function($container, config) {
        // Refresh widget content
        return $.get('/api/widget-data').then(function(data) {
            $container.html(mw.template('my-widget', config.template).render({
                message: data.message,
                color: config.color
            }));
        });
    },
    styles: ['ext.islamDashboard.widgets.myWidget.styles'],
    scripts: ['ext.islamDashboard.widgets.myWidget'],
    i18n: {
        'en': {
            'mywidget-title': 'My Widget',
            'mywidget-desc': 'A custom widget example'
        }
    }
});
```

###### `getWidget(id)`
Get a widget instance by ID.

**Parameters:**
- `id` (String): Widget instance ID

**Returns:** Widget instance or null if not found

### Navigation

#### `mw.islamDashboard.Navigation`
Manages dashboard navigation.

##### Methods

###### `addItem(sectionId, itemConfig)`
Add a navigation item to the specified section.

**Parameters:**
- `sectionId` (String): Section ID
- `itemConfig` (Object): Navigation item configuration
  - `id` (String): Unique item ID
  - `label` (String|mw.Message): Display text
  - `href` (String): Target URL
  - `icon` (String): Icon class (e.g., 'fa-home')
  - `permission` (String|Boolean): Required permission
  - `order` (Number): Sort order
  - `badge` (String|Number): Badge content
  - `class` (String): Additional CSS classes
  - `items` (Array): Child items

**Example:**
```javascript
// Add a navigation item
mw.islamDashboard.Navigation.addItem('main', {
    id: 'my-feature',
    label: mw.message('myfeature-title'),
    href: '/wiki/My_Feature',
    icon: 'fa-star',
    order: 100,
    permission: 'edit'
});
```

### Events

#### `mw.hook('islamdashboard.ready')`
Triggered when the dashboard is fully initialized.

**Example:**
```javascript
mw.hook('islamdashboard.ready').add(function(dashboard) {
    console.log('Dashboard ready:', dashboard);
});
```

#### `mw.hook('islamdashboard.widget.loaded')`
Triggered when a widget is loaded.

**Example:**
```javascript
mw.hook('islamdashboard.widget.loaded').add(function(widget) {
    console.log('Widget loaded:', widget);
});
```
## PHP API

### Widgets

#### `\MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget`
Abstract base class for all dashboard widgets.

##### Key Methods

###### `getType(): string`
Get the widget type identifier.

**Returns:** string - Unique widget type identifier

###### `getName(): \Message`
Get the widget's display name.

**Returns:** \Message - Localized widget name

###### `getDescription(): \Message`
Get the widget's description.

**Returns:** \Message - Localized widget description

###### `getIcon(): string`
Get the widget's icon class.

**Returns:** string - Icon class (e.g., 'fa-home')

###### `getContent(): string`
Render the widget's content.

**Returns:** string - HTML content

**Example:**
```php
class MyWidget extends \MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget {
    public static function getType(): string {
        return 'my-widget';
    }
    
    public function getName(): \Message {
        return wfMessage('mywidget-name');
    }
    
    public function getDescription(): \Message {
        return wfMessage('mywidget-desc');
    }
    
    public function getIcon(): string {
        return 'fa-star';
    }
    
    public function getContent(): string {
        $data = [
            'title' => $this->getName()->text(),
            'content' => 'This is my custom widget',
            'timestamp' => wfTimestampNow()
        ];
        
        return $this->renderTemplate('MyWidget', $data);
    }
}
```

### Navigation

#### `\MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager`
Manages the dashboard navigation system.

##### Key Methods

###### `getInstance(): self`
Get the singleton instance.

**Returns:** self - NavigationManager instance

###### `registerSection(NavigationSection $section): void`
Register a navigation section.

**Parameters:**
- `$section` (NavigationSection): Section to register

###### `getSection(string $id): ?NavigationSection`
Get a navigation section by ID.

**Parameters:**
- `$id` (string): Section ID

**Returns:** ?NavigationSection - Section or null if not found

**Example:**
```php
$navManager = \MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager::getInstance();

// Create a new section
$section = new \MediaWiki\Extension\IslamDashboard\Navigation\NavigationSection(
    'my-section',
    'mysection-label',
    'fa-puzzle-piece',
    100
);

// Add items to the section
$section->addItem(new \MediaWiki\Extension\IslamDashboard\Navigation\NavigationItem(
    'my-item',
    'myitem-label',
    'fa-star',
    Title::newFromText('My_Page')->getLinkURL(),
    'read',
    10
));

// Register the section
$navManager->registerSection($section);
```

### Hooks

#### `IslamDashboardNavigationInit`
Called when initializing the navigation system.

**Parameters:**
- `$navigationManager` (NavigationManager): Navigation manager instance

**Example:**
```php
$wgHooks['IslamDashboardNavigationInit'][] = function ( \MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager $navigationManager ) {
    // Add custom navigation items
    $section = new \MediaWiki\Extension\IslamDashboard\Navigation\NavigationSection(
        'custom',
        'custom-section',
        'fa-cog',
        1000
    );
    
    $navigationManager->registerSection($section);
    return true;
};
```

#### `IslamDashboardWidgets`
Called when registering widgets.

**Parameters:**
- `&$widgets` (array): Array of widget class names to register

**Example:**
```php
$wgHooks['IslamDashboardWidgets'][] = function ( &$widgets ) {
    $widgets[] = 'MyNamespace\MyCustomWidget';
    return true;
};
```

## Template System

The template system provides a simple way to render HTML templates with variable substitution.

### Basic Usage

1. Create a template file in `templates/` or `templates/widgets/`:
   ```html
   <!-- templates/widgets/MyWidget.mustache -->
   <div class="widget {{class}}">
       <h3>{{title}}</h3>
       <div class="widget-content">
           {{#content}}
               <p>{{.}}</p>
           {{/content}}
           {{^content}}
               <p>No content available.</p>
           {{/content}}
       </div>
       <div class="widget-footer">
           Last updated: {{timestamp}}
       </div>
   </div>
   ```

2. Render the template in PHP:
   ```php
   $data = [
       'title' => 'My Widget',
       'content' => ['First paragraph', 'Second paragraph'],
       'timestamp' => wfTimestampNow(),
       'class' => 'my-custom-widget'
   ];
   
   $html = $this->renderTemplate('MyWidget', $data);
   ```

### Template Features

- **Variable Substitution**: `{{variable}}`
- **Conditional Blocks**: `{{#condition}}...{{/condition}}`
- **Inverted Conditionals**: `{{^condition}}...{{/condition}}`
- **Iteration**: `{{#array}}...{{/array}}`
- **Partials**: `{{> partialName}}`
- **HTML Escaping**: `{{{unescapedHtml}}}`

## Best Practices

### Widget Development
1. **Keep widgets focused**: Each widget should have a single responsibility
2. **Use templates**: Separate presentation from logic
3. **Handle errors gracefully**: Show helpful error messages
4. **Support i18n**: All user-facing text should be localized
5. **Make them configurable**: Allow users to customize widget behavior

### Navigation
1. **Organize logically**: Group related items together
2. **Use icons**: Visual indicators improve usability
3. **Check permissions**: Only show items the user has access to
4. **Keep it simple**: Avoid deep nesting of menu items

### Performance
1. **Cache results**: Cache expensive operations
2. **Lazy load**: Load content as needed
3. **Minimize dependencies**: Only load what's necessary
4. **Optimize assets**: Minify CSS/JS and use sprites where possible

### Security
1. **Escape output**: Prevent XSS attacks
2. **Validate input**: Check all user input
3. **Check permissions**: Verify user has required rights
4. **Use CSRF tokens**: Protect against CSRF attacks
  - `template` (String|Function): Template string or function that returns HTML
  - `defaults` (Object): Default configuration values
  - `scripts` (Array): Array of script URLs to load
  - `styles` (Array): Array of stylesheet URLs to load
  - `onInit` (Function): Initialization callback
  - `onRender` (Function): Render callback
  - `onRefresh` (Function): Refresh callback
  - `onConfig` (Function): Configuration callback

**Example:**
```javascript
mw.islamDashboard.registerWidget('example-widget', {
    title: 'Example Widget',
    description: 'A sample widget',
    icon: 'icon-example',
    template: '<div class="example-widget">{{content}}</div>',
    defaults: {
        content: 'Hello, World!'
    },
    onRender: function($container, config) {
        // Custom rendering logic
    }
});
```

## PHP API

### `\MediaWiki\Extension\IslamDashboard\SpecialDashboard`
Main special page class for the dashboard.

#### Methods

##### `__construct()`
Initialize the special page.

##### `execute($subPage)`
Main entry point for the special page.

**Parameters:**
- `$subPage` (string|null): Subpage parameter

### `\MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget`
Base class for all dashboard widgets.

#### Methods

##### `__construct(IContextSource $context)`
Initialize the widget.

**Parameters:**
- `$context` (IContextSource): Context object

##### `getContent(): string`
Get the widget content.

**Returns:** string HTML content

##### `getTitle(): string`
Get the widget title.

**Returns:** string Title text

##### `getIcon(): string`
Get the widget icon.

**Returns:** string Icon name or HTML

##### `renderTemplate(string $templateName, array $data = []): string`
Render a template with the given data.

**Parameters:**
- `$templateName` (string): Template name without extension
- `$data` (array): Template variables

**Returns:** string Rendered HTML

### `\MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager`
Manages the navigation structure.

#### Methods

##### `getInstance(): self`
Get the singleton instance.

**Returns:** self

##### `registerNavigationSection(string $sectionId, array $sectionConfig): void`
Register a navigation section.

**Parameters:**
- `$sectionId` (string): Section identifier
- `$sectionConfig` (array): Section configuration

##### `registerNavigationItem(string $sectionId, string $itemId, array $itemConfig): void`
Register a navigation item within a section.

**Parameters:**
- `$sectionId` (string): Parent section ID
- `$itemId` (string): Item identifier
- `$itemConfig` (array): Item configuration

##### `getNavigationForUser(User $user): array`
Get navigation structure filtered by user permissions.

**Parameters:**
- `$user` (User): User object

**Returns:** array Navigation structure

## Widget System

The widget system allows for creating and managing dashboard widgets.

### Creating a Widget

1. Extend the `DashboardWidget` class
2. Implement required methods (`getContent()`, `getTitle()`, etc.)
3. Register the widget in `extension.json`

**Example:**
```php
class MyWidget extends \MediaWiki\Extension\IslamDashboard\Widgets\DashboardWidget {
    public function getContent(): string {
        return $this->renderTemplate('MyWidget', [
            'message' => 'Hello, World!'
        ]);
    }
    
    public function getTitle(): string {
        return $this->msg('mywidget-title')->text();
    }
    
    public function getIcon(): string {
        return 'puzzle';
    }
}
```

### Widget Registration

Register widgets in `extension.json`:
```json
"Widgets": {
    "MyWidget": "src/Widgets/MyWidget.php"
}
```

## Navigation System

The navigation system provides a flexible way to define and manage the dashboard navigation.

### Navigation Structure

```php
$navigation = [
    'section-id' => [
        'label' => 'Section Label',
        'icon' => 'icon-name',
        'permission' => 'required-permission',
        'items' => [
            'item-id' => [
                'label' => 'Item Label',
                'icon' => 'icon-name',
                'href' => '/path/to/page',
                'permission' => 'required-permission',
                'order' => 10
            ]
        ]
    ]
];
```

### Adding Navigation Items

```php
$navManager = \MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager::getInstance();

// Add a section
$navManager->registerNavigationSection('my-section', [
    'label' => 'My Section',
    'icon' => 'puzzle',
    'permission' => 'read',
    'items' => []
]);

// Add an item to the section
$navManager->registerNavigationItem('my-section', 'my-item', [
    'label' => 'My Item',
    'icon' => 'item-icon',
    'href' => '/wiki/MyPage',
    'permission' => 'read',
    'order' => 10
]);
```

## Template System

The template system provides a simple way to render HTML templates with variable substitution.

### Template Location

- Global templates: `templates/`
- Widget templates: `templates/widgets/`

### Template Syntax

Templates use a simple `{{variable}}` syntax for variable substitution.

**Example Template (`templates/MyWidget.mustache`):**
```html
<div class="my-widget">
    <h3>{{title}}</h3>
    <div class="content">
        {{content}}
    </div>
    {{#showFooter}}
    <div class="footer">
        {{footerText}}
    </div>
    {{/showFooter}}
</div>
```

### Rendering a Template

```php
$html = $this->renderTemplate('MyWidget', [
    'title' => 'My Widget',
    'content' => 'This is the widget content.',
    'showFooter' => true,
    'footerText' => 'Widget footer'
]);
```

## Hooks

### `IslamDashboardGetWidgets`
Allows modifying the list of available widgets.

**Parameters:**
- `&$widgets` (array): Array of widget definitions

**Example:**
```php
$wgHooks['IslamDashboardGetWidgets'][] = function( &$widgets ) {
    $widgets[] = [
        'id' => 'custom-widget',
        'class' => 'CustomWidget',
        'title' => wfMessage('customwidget-title')->text(),
        'description' => wfMessage('customwidget-desc')->text()
    ];
    return true;
};
```

## Events

### `islam-dashboard-widget-rendered`
Triggered when a widget is rendered.

**Properties:**
- `widgetId` (string): The ID of the rendered widget
- `element` (HTMLElement): The widget's DOM element

**Example:**
```javascript
$(document).on('islam-dashboard-widget-rendered', function(e, data) {
    console.log('Widget rendered:', data.widgetId, data.element);
});
```

## Examples

### Creating a Custom Widget

1. Create the widget class:
```php
// src/Widgets/HelloWorldWidget.php
namespace MediaWiki\Extension\IslamDashboard\Widgets;

class HelloWorldWidget extends DashboardWidget {
    public function getContent(): string {
        return $this->renderTemplate('HelloWorld', [
            'message' => 'Hello, World!',
            'timestamp' => wfTimestampNow()
        ]);
    }
    
    public function getTitle(): string {
        return $this->msg('helloworld-title')->text();
    }
    
    public function getIcon(): string {
        return 'greeting';
    }
}
```

2. Create the template:
```html
<!-- templates/widgets/HelloWorld.mustache -->
<div class="hello-world-widget">
    <div class="message">{{message}}</div>
    <div class="timestamp">{{timestamp}}</div>
</div>
```

3. Register the widget in `extension.json`:
```json
"Widgets": {
    "HelloWorld": "src/Widgets/HelloWorldWidget.php"
}
```

4. Add the widget to the dashboard:
```php
$dashboard = new \MediaWiki\Extension\IslamDashboard\SpecialDashboard();
$dashboard->addWidget('HelloWorld', ['position' => 'main']);
```

### `WidgetManager`
Main class for managing widgets.

#### Methods

##### `getInstance()`
Get the singleton instance.

**Returns:** WidgetManager instance

##### `registerWidget($class)`
Register a widget class.

**Parameters:**
- `$class` (string): Fully qualified class name

**Throws:**
- `InvalidArgumentException` if class doesn't exist or doesn't extend DashboardWidget

##### `getWidgets()`
Get all registered widgets.

**Returns:** array of widget instances

## Widget API

### `DashboardWidget`
Base class for all dashboard widgets.

#### Properties

##### `$config`
Widget configuration array.

#### Methods

##### `getType()`
Get the widget type.

**Returns:** string

##### `getName()`
Get the widget name.

**Returns:** Message

##### `getDescription()`
Get the widget description.

**Returns:** Message

##### `render()`
Render the widget content.

**Returns:** string HTML

## Hooks

### `IslamDashboardRegisterWidgets`
Called when widgets are being registered.

**Parameters:**
- `&$widgets` (array): Reference to widget instances array

**Example:**
```php
$wgHooks['IslamDashboardRegisterWidgets'][] = function( &$widgets ) {
    $widgets[] = new MyCustomWidget();
    return true;
};
```

### `IslamDashboardBeforeRender`
Called before the dashboard is rendered.

**Parameters:**
- `$context` (IContextSource): Context object
- `$output` (OutputPage): Output page object

## Events

### `islamdashboard.widgets.registered`
Fired when all widgets have been registered.

**Properties:**
- `widgets` (Array): List of registered widgets

### `islamdashboard.layout.changed`
Fired when the dashboard layout changes.

**Properties:**
- `layout` (Object): New layout configuration

## Examples

### Registering a New Widget
```php
// In extension.json
"Hooks": {
    "IslamDashboardRegisterWidgets": "MyExtensionHooks::onRegisterWidgets"
}

// In MyExtensionHooks.php
public static function onRegisterWidgets( &$widgets ) {
    $widgets[] = new MyCustomWidget();
    return true;
}
```

### JavaScript Widget Interaction
```javascript
// Get widget manager
const widgetManager = mw.islamDashboard.getWidgetManager();

// Add a widget
widgetManager.addWidget('example-widget', 'main', 0);

// Remove a widget
widgetManager.removeWidget('example-widget-1');

// Save layout
widgetManager.saveLayout();
```

### Custom Widget Implementation
```php
class MyCustomWidget extends DashboardWidget {
    public static function getType() {
        return 'my-custom-widget';
    }

    public function getName() {
        return wfMessage('mycustomwidget-name');
    }

    public function render() {
        return Html::element('div', ['class' => 'my-widget'], 'Hello, World!');
    }
}
```

## Error Handling
All API methods throw appropriate exceptions for error conditions. Always wrap API calls in try-catch blocks.

## Versioning
The API follows Semantic Versioning (SemVer). Breaking changes will be documented in the release notes.

## See Also
- [Widget Development Guide](./WIDGETS.md)
- [Architecture](./ARCHITECTURE.md)
- [Configuration Guide](./CONFIGURATION.md)
