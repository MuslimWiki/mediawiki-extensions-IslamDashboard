# IslamDashboard Architecture

## Overview

The IslamDashboard extension follows a modular architecture designed for extensibility and maintainability. It's built on top of MediaWiki's extension system and integrates with the Islam Skin while maintaining compatibility with other skins.

## Core Components

### 1. Entry Points
- `IslamDashboard.php`: Main extension file with hooks and setup
- `SpecialIslamDashboard.php`: Special page for the main dashboard interface
- `Hooks/`: Contains all hook handlers

### 2. Backend
- `Api/`: API modules for AJAX operations
- `Data/`: Data access layer and services
- `Modules/`: Individual dashboard modules
  - `ProfileModule.php`
  - `ActivityModule.php`
  - `NotificationsModule.php`
  - `QuickActionsModule.php`

### 3. Frontend
- `resources/`
  - `modules/`: Client-side JavaScript
  - `styles/`: CSS/Less files
  - `templates/`: Client-side templates

### 4. Integration
- `includes/`
  - `Hooks/`: Integration hooks with other extensions
  - `Widgets/`: Reusable UI components

## Data Flow

1. User requests the dashboard (Special:Dashboard)
2. SpecialIslamDashboard class initializes
3. Loads user-specific configuration
4. Fetches data for enabled modules
5. Renders the dashboard using the appropriate template
6. Client-side JavaScript enhances interactivity

## Security Considerations
- All API endpoints require proper permissions
- User data is scoped to the current user
- Input validation on all endpoints
- CSRF protection for all write operations

## Performance
- Lazy loading of non-critical modules
- Client-side caching where appropriate
- Database query optimization
- Asset bundling and minification

## Extension Points
- Hooks for adding custom modules
- Widget system for reusable UI components
- Theme variables for styling customization
