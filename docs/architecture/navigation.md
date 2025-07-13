# Navigation System Technical Specification - IslamDashboard

## 1. Overview

The navigation system provides a hierarchical, collapsible menu for the IslamDashboard, allowing users to access various dashboard features and widgets. The system is designed to be:

- **Extensible**: Easy to add new navigation items and sections
- **Responsive**: Works on all device sizes
- **Accessible**: Follows WAI-ARIA best practices
- **Performant**: Lazy loads content where appropriate
- **Maintainable**: Clean, well-documented code
- **Internationalized**: Supports multiple languages
- **Themable**: Customizable appearance through CSS variables

## 2. Architecture

### 2.1. Components

#### 2.1.1. NavigationManager
- **Purpose**: Central controller for the navigation system
- **Responsibilities**:
  - Manages navigation state and structure
  - Handles registration of navigation items and sections
  - Manages permissions and visibility
  - Handles persistence of user preferences (collapsed/expanded states)
  - Provides methods for dynamic navigation updates

#### 2.1.2. NavigationItem
- **Purpose**: Represents a single navigation item
- **Properties**:
  - `id` (string): Unique identifier
  - `label` (Message|string): Display text
  - `icon` (string): Icon class (e.g., 'fa-home' for FontAwesome)
  - `url` (string|array): Target URL or route
  - `permission` (string|bool): Required permission to view
  - `order` (int): Sort order
  - `isActive` (bool): Whether the item is currently active
  - `children` (NavigationItem[]): Child items
  - `badge` (string|int): Optional badge/count
  - `class` (string): Additional CSS classes

#### 2.1.3. NavigationSection
- **Purpose**: Groups related NavigationItems
- **Properties**:
  - `id` (string): Unique identifier
  - `label` (Message|string): Section title
  - `icon` (string): Section icon
  - `items` (NavigationItem[]): Child items
  - `isCollapsed` (bool): Whether section is collapsed
  - `order` (int): Sort order
  - `permission` (string|bool): Required permission to view section

#### 2.1.4. NavigationRenderer
- **Purpose**: Handles HTML generation and rendering
- **Features**:
  - Supports different rendering modes (desktop, mobile, sidebar)
  - Handles accessibility attributes (ARIA)
  - Implements responsive behavior
  - Supports theming through CSS classes

### 2.2. Data Flow

1. **Initialization**
   - NavigationManager loads navigation structure from configuration
   - Filters items based on user permissions
   - Renders the navigation menu
   - Loads saved user preferences (collapsed/expanded states)

2. **User Interaction**
   - User interacts with navigation (expand/collapse, click items)
   - JavaScript handles client-side interactions
   - State changes are saved to user preferences via API
   - Active state is updated based on current route

3. **Dynamic Updates**
   - Plugins/widgets can register new navigation items
   - Navigation updates are reflected in real-time
   - Permission changes immediately affect visibility

## 3. Implementation Details

### 3.1. Basic Usage

#### Creating Navigation Items

```php
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationManager;
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationItem;
use MediaWiki\Extension\IslamDashboard\Navigation\NavigationSection;

// Get the NavigationManager instance
$navManager = NavigationManager::getInstance();

// Create a section
$dashboardSection = new NavigationSection(
    'dashboard',
    'islamdashboard-dashboard', // i18n message key
    'fa-tachometer-alt',
    10 // Order
);

// Add items to the section
$dashboardSection->addItem(
    new NavigationItem(
        'dashboard-home',
        'islamdashboard-dashboard-home',
        'fa-home',
        [ 'Special', 'Dashboard' ],
        'view-dashboard',
        10
    )
);

// Register the section with the manager
$navManager->registerSection($dashboardSection);
```

### 3.2. JavaScript API

The navigation system includes a JavaScript module for client-side interactions:

```javascript
// Get the navigation instance
const navigation = require('ext.islamDashboard.navigation');

// Initialize navigation
navigation.init();

// Add a new item dynamically
navigation.addItem({
    id: 'custom-item',
    section: 'dashboard',
    label: 'Custom Item',
    icon: 'fa-star',
    url: '/wiki/Custom_Page',
    order: 100
});

// Handle navigation events
mw.hook('islamdashboard.navigation').add(function(data) {
    if (data.action === 'navigate') {
        console.log('Navigating to:', data.url);
    }
});
```

### 3.3. Theming and Styling

The navigation system uses CSS variables for theming. Override these variables to customize the appearance:

```less
// Example theme overrides
.ext-islamdashboard-navigation {
    --nav-bg: #2c3e50;
    --nav-text: #ecf0f1;
    --nav-hover: #34495e;
    --nav-active: #3498db;
    --nav-border: #1a252f;
    --nav-width: 250px;
    --nav-collapsed-width: 60px;
}
```

## 4. Integration with Dashboard

The navigation system is tightly integrated with the IslamDashboard extension:

1. **Special:Dashboard**
   - Renders the main navigation
   - Handles routing and active states
   - Manages responsive behavior

2. **Widgets**
   - Widgets can register navigation items
   - Context-aware navigation updates
   - Dynamic content loading

3. **User Preferences**
   - Saves navigation state per user
   - Syncs across devices (if applicable)
   - Respects user permissions

## 5. Performance Considerations

1. **Lazy Loading**
   - Navigation items are loaded on demand
   - Icons and other assets are lazy-loaded
   - Sections can be loaded asynchronously

2. **Caching**
   - Navigation structure is cached
   - User-specific preferences are cached
   - Clears cache on permission changes

3. **Optimizations**
   - Minimal DOM updates
   - Efficient event delegation
   - Debounced resize handlers

## 6. Accessibility

The navigation system follows WAI-ARIA best practices:

```html
<nav class="ext-islamdashboard-navigation" role="navigation" aria-label="Main">
    <ul class="nav-sections" role="menubar">
        <li class="nav-section" role="none">
            <button class="nav-section-header" 
                    role="menuitem" 
                    aria-expanded="true" 
                    aria-controls="section-dashboard">
                <i class="fa fa-tachometer-alt"></i>
                <span class="nav-label">Dashboard</span>
            </button>
            <ul id="section-dashboard" class="nav-items" role="menu">
                <li class="nav-item" role="none">
                    <a href="/wiki/Special:Dashboard" 
                       class="nav-link" 
                       role="menuitem" 
                       aria-current="page">
                        <i class="fa fa-home"></i>
                        <span class="nav-label">Home</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
```

## 7. Testing

### 7.1. Unit Tests

- Navigation structure
- Permission checks
- URL generation
- State management

### 7.2. Integration Tests

- Rendering in different contexts
- User interactions
- Responsive behavior
- Accessibility validation

### 7.3. Browser Tests

- Cross-browser compatibility
- Performance metrics
- Visual regression testing

## 8. Security Considerations

1. **Permission Checks**
   - Server-side validation of all permissions
   - Client-side checks for better UX
   - Secure storage of user preferences

2. **Input Sanitization**
   - Escape all user-generated content
   - Validate all navigation URLs
   - Prevent XSS attacks

3. **CSRF Protection**
   - Secure API endpoints
   - Nonce validation
   - Rate limiting

## 9. Extensibility

The navigation system can be extended through:

1. **Hooks**
   - `IslamDashboardNavigationInit`
   - `IslamDashboardNavigationItems`
   - `IslamDashboardNavigationSections`

2. **Events**
   - `islamdashboard.navigation.ready`
   - `islamdashboard.navigation.navigate`
   - `islamdashboard.navigation.toggle`

3. **Plugins**
   - Custom navigation renderers
   - Alternative navigation layouts
   - Advanced filtering and search

## 10. Future Enhancements

1. **Nested Navigation**
   - Support for multi-level menus
   - Mega-menu support
   - Contextual navigation

2. **Search Integration**
   - Global search within navigation
   - Quick access to frequent items
   - Command palette

3. **Personalization**
   - User-customizable navigation
   - Drag-and-drop reordering
   - Saved views

## 11. Troubleshooting

### Common Issues

1. **Missing Navigation Items**
   - Check user permissions
   - Verify navigation registration
   - Clear cache if needed

2. **JavaScript Errors**
   - Check browser console
   - Verify module loading
   - Check for conflicts

3. **Styling Issues**
   - Check CSS specificity
   - Verify theme variables
   - Test in multiple browsers

### Debugging

Enable debug mode in LocalSettings.php:

```php
$wgDebugLogGroups['IslamDashboardNavigation'] = __DIR__ . '/logs/navigation-debug.log';
```

## 12. Best Practices

1. **For Developers**
   - Keep navigation items focused and minimal
   - Group related items logically
   - Use meaningful icons and labels
   - Test with different permission levels

2. **For Designers**
   - Follow the design system
   - Ensure sufficient color contrast
   - Test touch targets on mobile
   - Provide visual feedback for interactions

3. **For Content Editors**
   - Use clear, concise labels
   - Organize items hierarchically
   - Consider the user journey
   - Test with real users
   - Main content is loaded via AJAX for better performance
   - URL is updated using History API
   - Loading states are shown during content loading

## 3. Implementation Details

### 3.1. Navigation Structure

```php
$navigation = [
    'dashboard' => [
        'label' => 'Dashboard',
        'icon' => 'dashboard',
        'items' => [
            'overview' => [
                'label' => 'Overview',
                'url' => SpecialPage::getTitleFor('Dashboard')->getLocalURL(),
                'icon' => 'home',
                'permission' => 'view-dashboard',
                'order' => 10
            ],
            // More items...
        ]
    ],
    'content' => [
        'label' => 'Content',
        'icon' => 'article',
        'items' => [
            // Content items...
        ]
    ]
    // More sections...
];
```

### 3.2. JavaScript API

```javascript
// Initialize navigation
const navigation = new NavigationManager({
    container: '#dashboard-navigation',
    onItemClick: (item) => {
        // Handle navigation
    },
    onSectionToggle: (sectionId, isCollapsed) => {
        // Save state
    }
});

// Add dynamic items
navigation.addItem('content', {
    id: 'new-page',
    label: 'New Page',
    icon: 'add',
    url: '/wiki/Special:CreatePage',
    permission: 'createpage'
});
```

### 3.3. CSS Structure

```css
/* Main navigation container */
.dashboard-navigation {
    /* Styles */
}

/* Navigation sections */
.nav-section {
    /* Styles */
}

/* Section header */
.nav-section-header {
    /* Styles */
}

/* Navigation items */
.nav-item {
    /* Styles */
}

/* Active state */
.nav-item--active {
    /* Styles */
}

/* Icons */
.nav-icon {
    /* Styles */
}
```

## 4. Integration with Existing Code

### 4.1. SpecialDashboard Updates

1. Update `getNavigationMenuHTML()` to use the new NavigationManager
2. Add methods for handling AJAX content loading
3. Update templates to include the new navigation structure

### 4.2. Widget Integration

Widgets can register navigation items using hooks:

```php
$wgHooks['IslamDashboardNavigation'][] = function( &$navigation ) {
    $navigation['my-widget'] = [
        'label' => 'My Widget',
        'url' => SpecialPage::getTitleFor('MyWidget')->getLocalURL(),
        'icon' => 'widget',
        'permission' => 'view-my-widget',
        'order' => 100
    ];
    return true;
};
```

## 5. Performance Considerations

1. **Lazy Loading**
   - Load navigation items asynchronously
   - Load content when needed

2. **Caching**
   - Cache rendered navigation
   - Invalidate cache when permissions change

3. **Optimizations**
   - Minimize DOM updates
   - Use event delegation
   - Optimize icons and images

## 6. Accessibility

1. **Keyboard Navigation**
   - Tab through menu items
   - Enter/space to activate
   - Arrow keys for navigation

2. **ARIA Attributes**
   - `role="navigation"`
   - `aria-expanded` for collapsible sections
   - `aria-current` for active items

3. **Focus Management**
   - Manage focus when expanding/collapsing
   - Return focus to trigger after closing

## 7. Testing

1. **Unit Tests**
   - Navigation structure
   - Permission checks
   - URL generation

2. **Integration Tests**
   - Navigation rendering
   - User interactions
   - Content loading

3. **Browser Tests**
   - Cross-browser compatibility
   - Responsive behavior
   - Accessibility

## 8. Documentation

1. **Developer Documentation**
   - Navigation structure
   - API reference
   - Customization guide

2. **User Documentation**
   - How to use navigation
   - Customization options
   - Troubleshooting

## 9. Future Enhancements

1. **Nested Navigation**
   - Support for multi-level menus
   - Mega menus for complex navigation

2. **Customization**
   - Allow users to reorder items
   - Custom icons and colors

3. **Analytics**
   - Track navigation usage
   - Heatmaps

4. **Theming**
   - Support for custom themes
   - Dark/light mode

## 10. Implementation Plan

1. **Phase 1: Core Functionality**
   - Implement NavigationManager and basic components
   - Basic styling and responsiveness
   - Basic keyboard navigation

2. **Phase 2: Advanced Features**
   - AJAX content loading
   - Persistence of user preferences
   - Advanced keyboard navigation

3. **Phase 3: Polish and Optimization**
   - Performance optimizations
   - Accessibility improvements
   - Browser testing

4. **Phase 4: Documentation and Release**
   - Write documentation
   - Create examples
   - Release and gather feedback
