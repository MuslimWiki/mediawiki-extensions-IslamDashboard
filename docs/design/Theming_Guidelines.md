# IslamDashboard Theming Guidelines

## Version: 0.3.1

## Overview
This document provides guidelines for theming the IslamDashboard to ensure consistency and maintainability while allowing for customization.

## Core Principles
1. **Progressive Enhancement**: Core functionality must work without JavaScript
2. **Accessibility First**: WCAG 2.1 AA compliance
3. **Responsive Design**: Mobile-first approach
4. **Performance**: Minimal CSS with efficient selectors

## Color System

### Primary Colors
- `--color-primary`: #0645ad
- `--color-primary-dark`: #053d8b
- `--color-primary-light`: #e6f0ff

### Semantic Colors
- `--color-success`: #28a745
- `--color-warning`: #ffc107
- `--color-error`: #dc3545
- `--color-info`: #17a2b8

### Grayscale
- `--color-gray-100`: #f8f9fa
- `--color-gray-200`: #e9ecef
- `--color-gray-500`: #6c757d
- `--color-gray-900`: #212529

## Typography

### Font Stack
```css
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
```

### Scale
- Base: 1rem (16px)
- H1: 2.5rem
- H2: 2rem
- H3: 1.75rem
- H4: 1.5rem
- Body: 1rem
- Small: 0.875rem

## Spacing System
- Base unit: 0.25rem (4px)
- Scale: 0.25rem, 0.5rem, 1rem, 1.5rem, 2rem, 3rem, 4rem

## Components

### Buttons
```css
.button {
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.button-primary {
    background: var(--color-primary);
    color: white;
}

.button-primary:hover {
    background: var(--color-primary-dark);
}
```

### Cards
```css
.card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
```

## Customization

### CSS Variables
Override these variables in your skin or extension:
```css
:root {
    --color-primary: #your-color;
    --spacing-unit: 0.25rem;
    /* Other overrides */
}
```

### Theme Hooks
```php
$wgHooks['IslamDashboardThemeConfig'][] = function( &$themeConfig ) {
    $themeConfig['colors']['primary'] = '#your-color';
    return true;
};
```

## Dark Mode
```css
@media (prefers-color-scheme: dark) {
    :root {
        --color-background: #1a1a1a;
        --color-text: #e0e0e0;
        /* Other dark mode variables */
    }
}
```

## Best Practices
1. Use CSS variables for theming
2. Prefer flexbox/grid for layouts
3. Use relative units (rem, em) for typography and spacing
4. Test with high contrast modes
5. Support RTL languages

## Version History
- **0.3.1**: Initial version
