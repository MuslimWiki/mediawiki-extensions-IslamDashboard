# Changelog

All notable changes to the IslamDashboard extension will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.3.1] - 2025-07-14
### Added
- Added IslamCore as a required dependency for shared services and security
- New migration guide for upgrading to version 0.3.1
- Documentation updates to reflect new architecture
- Integration with IslamCore's widget management system
- New hooks for extension developers
- Enhanced security features through IslamCore
- Improved internationalization support with RTL language handling

### Changed
- Moved shared services and utilities to IslamCore
- Updated widget system to use IslamCore's widget manager
- Refactored configuration to work with IslamCore's settings
- Updated documentation to reference IslamCore
- Improved error handling and logging using IslamCore's services

### Fixed
- Resolved widget loading issues with new architecture
- Fixed compatibility with IslamCore's security model
- Addressed performance issues in widget rendering

## [0.3.0] - 2025-07-14
### Added
- Proper special page registration for IslamDashboard
- Dependency injection for database connections using ILoadBalancer
- Factory pattern implementation for service instantiation
- Comprehensive error handling for recent changes display
- Detailed architecture documentation in `docs/architecture/`
- Development guides in `docs/development/`
- API documentation in `docs/api/`
- Release notes for version 0.3.0

### Changed
- Updated to use MediaWiki 1.43+ coding standards
- Refactored database queries to use modern SelectQueryBuilder
- Improved title handling and validation
- Updated layout structure in preparation for three-column display (not yet functional)
- Reorganized documentation structure for better maintainability
- Updated README.md with current project structure and documentation links

### Fixed
- Resolved special page visibility issues
- Fixed database query compatibility with MediaWiki 1.43+
- Addressed all deprecation warnings
- Fixed documentation links and references
- Removed duplicate service registrations in Hooks.php

### Known Issues
- Three-column display is not yet functional (planned for next release)
- Some layout and styling issues may be present in the dashboard interface

## [0.2.2] - 2025-07-13
### Added
- Comprehensive documentation for API endpoints and usage
- Widget development guide with examples and best practices
- Internationalization and localization documentation
- Testing guide with PHPUnit and QUnit examples
- Versioning and release process documentation
- Code of conduct and contribution guidelines

### Changed
- Restructured documentation directory for better organization
- Updated all documentation to follow MediaWiki standards
- Improved code examples and added more detailed explanations
- Standardized documentation format and style

## [0.2.1] - 2025-07-13
### Fixed
- Fixed dashboard layout to properly display main content next to sidebar navigation
- Resolved issue with raw CSS being output in the page
- Corrected HTML structure for proper flexbox layout

### Changed
- Moved all inline styles to dedicated LESS files
- Updated ResourceLoader configuration for better style management
- Improved responsive behavior of dashboard components

## [Unreleased]
### Changed
- Updated hook system to use HookContainer instead of $wgHooks
- Improved test coverage for navigation and widget systems
- Enhanced documentation for hook usage and testing

## [0.1.0] - 2025-07-12
### Added
- Comprehensive widget system with Mustache template support
- Resource organization following MediaWiki conventions
- Documentation for widget development and theming
- Unit tests for core components
- PHPUnit test suite for server-side components
- QUnit tests for client-side JavaScript
- Widget configuration system
- Internationalization support for all user-facing text
- Comprehensive API documentation
- Development and contribution guidelines

### Changed
- Migrated all widget resources to `resources/widgets/`
- Organized widget styles in `resources/styles/widgets/`
- Updated all widget classes to use Mustache templates
- Improved error handling and validation
- Enhanced widget registration system
- Updated documentation to reflect new architecture
- Optimized resource loading
- Improved code organization and structure
- Standardized coding style across the codebase

## [0.0.2] - 2025-07-11
https://github.com/MuslimWiki/IslamDashboard/compare/v0.0.1...v0.0.2
### Added
- Comprehensive navigation system with collapsible sections
- NavigationManager for managing navigation structure and permissions
- NavigationRenderer for generating accessible HTML
- Client-side JavaScript for interactive navigation
- Responsive design for mobile and desktop views
- User preference persistence for navigation state
- Integration points for widgets and extensions
- Documentation for the navigation system

### Changed
- Renamed SpecialDashboard to SpecialIslamDashboard for consistency with MediaWiki naming conventions
- Updated all references in code, tests, and documentation
- Fixed PSR-4 autoloading in composer.json
- Improved accessibility with ARIA attributes
- Enhanced user experience with smooth animations

## [0.0.1] - 2025-07-10
https://github.com/MuslimWiki/IslamDashboard/releases/tag/v0.0.1
### Added
- Initial release of IslamDashboard
- Basic dashboard layout with widget support
- Example widgets (Welcome, Recent Activity, Quick Actions)
- Widget management system
- Basic theming and styling
- Extension framework and architecture
- Documentation and configuration guides
- Internationalization support

