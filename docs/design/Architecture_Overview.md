# IslamDashboard Architecture Overview

## Version: 0.3.1

## Table of Contents
1. [Technical Stack](#technical-stack)
2. [Directory Structure](#directory-structure)
3. [Autoloading](#autoloading)
4. [Component Architecture](#component-architecture)
5. [Data Flow](#data-flow)
6. [Security Model](#security-model)
7. [Performance Considerations](#performance-considerations)
8. [Compatibility](#compatibility)

## Technical Stack

### Core Technologies
- **PHP**: 7.4+ (MediaWiki 1.43+ compatible)
- **JavaScript**: ES6+ with Babel transpilation
- **CSS**: Codex with custom theming support
- **Templating**: PHP templates with OOUI for complex UIs

### Modern Frontend Options
While maintaining MediaWiki 1.43+ compatibility, we can leverage:
1. **Vue.js** (Recommended)
   - Lightweight and easy to integrate
   - Can be loaded as a ResourceLoader module
   - Good documentation and community support

2. **React** (Alternative)
   - More powerful but heavier
   - Requires additional build steps
   - Better for complex UIs

## Directory Structure

```
extensions/IslamDashboard/
├── docs/                    # Documentation
├── includes/                # PHP classes
│   ├── Api/                 # API endpoints
│   ├── Hooks/               # Hook handlers
│   ├── Widgets/             # Dashboard widgets
│   └── IslamDashboard.php   # Main extension class
├── resources/               # Frontend resources
│   ├── lib/                 # Third-party libraries
│   ├── src/                 # Source files
│   │   ├── components/      # Vue/React components
│   │   ├── styles/          # CSS/LESS files
│   │   └── utils/           # Utility functions
│   └── dist/                # Built assets
├── tests/                   # Test suites
│   ├── phpunit/             # PHPUnit tests
│   └── qunit/               # QUnit tests
├── i18n/                    # Internationalization
└── extension.json           # Extension manifest
```

## Autoloading

### PSR-4 Autoloading
```json
{
    "autoload": {
        "psr-4": {
            "MediaWiki\\Extension\\IslamDashboard\\": "includes/",
            "MediaWiki\\Extension\\IslamDashboard\\Widgets\\": "includes/Widgets/"
        }
    }
}
```

### Class Naming Conventions
- Use proper namespacing: `MediaWiki\Extension\IslamDashboard\`
- One class per file
- File names match class names (case-sensitive)

## Component Architecture

### Backend Components
1. **Widget System**
   - Base Widget class
   - Widget registry
   - Data providers

2. **API Layer**
   - RESTful endpoints
   - Data validation
   - Error handling

3. **Service Layer**
   - Business logic
   - Data access
   - Caching

### Frontend Components
1. **Core UI**
   - Layout manager
   - Theme provider
   - Widget renderer

2. **State Management**
   - Centralized store
   - Reactive updates
   - Persistence layer

## Data Flow

1. **Initial Load**
   ```
   Browser → MediaWiki → SpecialPage → Widgets → HTML/JS/CSS
   ```

2. **Dynamic Updates**
   ```
   JavaScript → API → Service → Database
   Service → API → JavaScript → DOM
   ```

## Security Model

### Authentication
- MediaWiki session management
- CSRF protection
- Rate limiting

### Authorization
- Role-based access control
- Permission checks
- Data validation

## Performance Considerations

### Frontend
- Lazy loading of components
- Efficient DOM updates
- Asset optimization

### Backend
- Query optimization
- Caching strategy
- Batch processing

## Compatibility

### MediaWiki
- Core: 1.43+
- PHP: 7.4+
- Database: MySQL 5.7+, PostgreSQL 10+, SQLite 3.8+

### Dependencies
- Codex: Latest stable
- OOUI: As bundled with MediaWiki
- Vue.js: 3.x (optional)

## Version History
- **0.3.1**: Initial version
